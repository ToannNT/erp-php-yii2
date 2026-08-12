#!/usr/bin/env bash
#
# Backup MySQL (app riêng trên CapRover) -> gzip -> Cloudflare R2.
# Chạy TRÊN HOST (không trong container app), bằng cron. Xem README.md.
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

log() { printf '[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*"; }
die() { log "LỖI: $*" >&2; exit 1; }

[ -f "$SCRIPT_DIR/backup.env" ] || die "Chưa có $SCRIPT_DIR/backup.env (copy từ backup.env.example)."
# shellcheck source=/dev/null
. "$SCRIPT_DIR/backup.env"

: "${MYSQL_APP_NAME:?Thiếu MYSQL_APP_NAME trong backup.env}"
: "${MYSQL_DATABASE:?Thiếu MYSQL_DATABASE}"
: "${MYSQL_USER:?Thiếu MYSQL_USER}"
: "${MYSQL_PASSWORD:?Thiếu MYSQL_PASSWORD}"
: "${RCLONE_REMOTE:?Thiếu RCLONE_REMOTE}"
: "${R2_BUCKET:?Thiếu R2_BUCKET}"

command -v docker >/dev/null || die "Không tìm thấy docker."
command -v rclone >/dev/null || die "Không tìm thấy rclone (xem README để cài)."

TS="$(date '+%Y%m%d-%H%M%S')"
TMP_DIR="$(mktemp -d)"
trap 'rm -rf "$TMP_DIR"' EXIT

DUMP_NAME="${MYSQL_DATABASE}-${TS}.sql"
DUMP="$TMP_DIR/$DUMP_NAME"

log "Tìm container MySQL của app '$MYSQL_APP_NAME'..."
CONTAINER="$(docker ps -q --filter "name=srv-captain--${MYSQL_APP_NAME}" | head -n1)"
[ -n "$CONTAINER" ] || die "Không thấy container đang chạy khớp 'srv-captain--${MYSQL_APP_NAME}'. Kiểm tra lại MYSQL_APP_NAME."

# --single-transaction: snapshot nhất quán cho InnoDB mà KHÔNG lock ghi (site vẫn chạy bình thường).
# MYSQL_PWD truyền qua env để mật khẩu không lộ trong `ps aux` của container.
log "Dump database '$MYSQL_DATABASE' từ container ${CONTAINER}..."
docker exec -e MYSQL_PWD="$MYSQL_PASSWORD" "$CONTAINER" \
    mysqldump \
        --single-transaction \
        --quick \
        --routines \
        --triggers \
        --events \
        --default-character-set=utf8mb4 \
        -u "$MYSQL_USER" \
        "$MYSQL_DATABASE" > "$DUMP"

# mysqldump có thể chết giữa đường và vẫn để lại file (hỏng, không dùng được).
# Nó luôn ghi "-- Dump completed" ở dòng cuối khi thành công -> dùng làm mốc kiểm tra.
[ -s "$DUMP" ] || die "Dump rỗng."
tail -n 5 "$DUMP" | grep -q 'Dump completed' \
    || die "Dump không hoàn tất (thiếu marker 'Dump completed') — KHÔNG upload file hỏng."

log "Dump OK ($(du -h "$DUMP" | cut -f1)). Đang nén..."
gzip -9 "$DUMP"
DUMP_GZ="${DUMP}.gz"

log "Upload lên ${RCLONE_REMOTE}:${R2_BUCKET}/db/ ..."
# --s3-no-check-bucket: R2 token thường không có quyền list/tạo bucket -> tránh rclone fail khi tự kiểm tra.
rclone copy "$DUMP_GZ" "${RCLONE_REMOTE}:${R2_BUCKET}/db/" --s3-no-check-bucket

if [ -n "${LOCAL_BACKUP_DIR:-}" ]; then
    mkdir -p "$LOCAL_BACKUP_DIR"
    cp "$DUMP_GZ" "$LOCAL_BACKUP_DIR/"
    log "Đã giữ bản local tại $LOCAL_BACKUP_DIR/$(basename "$DUMP_GZ")"
    if [ -n "${KEEP_LOCAL_DAYS:-}" ]; then
        find "$LOCAL_BACKUP_DIR" -name "${MYSQL_DATABASE}-*.sql.gz" -type f \
            -mtime "+${KEEP_LOCAL_DAYS}" -delete
        log "Đã xoá bản local cũ hơn ${KEEP_LOCAL_DAYS} ngày."
    fi
fi

log "HOÀN TẤT: db/${DUMP_NAME}.gz"
