#!/usr/bin/env bash
#
# Restore database từ bản backup .sql.gz (local hoặc tải từ R2).
#
# THAO TÁC NÀY GHI ĐÈ DỮ LIỆU HIỆN TẠI -> bắt buộc truyền --yes.
#
# Dùng:
#   ./restore-db.sh --list                          # liệt kê bản backup trên R2
#   ./restore-db.sh --from-r2 erp-20260729-030501.sql.gz --yes
#   ./restore-db.sh --file /path/local.sql.gz --yes
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

log() { printf '[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*"; }
die() { log "LỖI: $*" >&2; exit 1; }

[ -f "$SCRIPT_DIR/backup.env" ] || die "Chưa có $SCRIPT_DIR/backup.env."
# shellcheck source=/dev/null
. "$SCRIPT_DIR/backup.env"

: "${MYSQL_APP_NAME:?Thiếu MYSQL_APP_NAME}"
: "${MYSQL_DATABASE:?Thiếu MYSQL_DATABASE}"
: "${MYSQL_USER:?Thiếu MYSQL_USER}"
: "${MYSQL_PASSWORD:?Thiếu MYSQL_PASSWORD}"
: "${RCLONE_REMOTE:?Thiếu RCLONE_REMOTE}"
: "${R2_BUCKET:?Thiếu R2_BUCKET}"

SRC_FILE=""
FROM_R2=""
CONFIRMED=""

while [ $# -gt 0 ]; do
    case "$1" in
        --list)
            rclone lsl "${RCLONE_REMOTE}:${R2_BUCKET}/db/" --s3-no-check-bucket
            exit 0
            ;;
        --file)    SRC_FILE="${2:?Thiếu đường dẫn sau --file}"; shift 2 ;;
        --from-r2) FROM_R2="${2:?Thiếu tên file sau --from-r2}"; shift 2 ;;
        --yes)     CONFIRMED=1; shift ;;
        *) die "Tham số không hợp lệ: $1" ;;
    esac
done

[ -n "$CONFIRMED" ] || die "Thiếu --yes. Lệnh này GHI ĐÈ database '${MYSQL_DATABASE}'."
[ -n "$SRC_FILE" ] || [ -n "$FROM_R2" ] || die "Cần --file <path> hoặc --from-r2 <tên file>."

TMP_DIR="$(mktemp -d)"
trap 'rm -rf "$TMP_DIR"' EXIT

if [ -n "$FROM_R2" ]; then
    log "Tải ${FROM_R2} từ R2..."
    rclone copy "${RCLONE_REMOTE}:${R2_BUCKET}/db/${FROM_R2}" "$TMP_DIR/" --s3-no-check-bucket
    SRC_FILE="$TMP_DIR/${FROM_R2}"
fi

[ -f "$SRC_FILE" ] || die "Không thấy file $SRC_FILE."

log "Tìm container MySQL của app '$MYSQL_APP_NAME'..."
CONTAINER="$(docker ps -q --filter "name=srv-captain--${MYSQL_APP_NAME}" | head -n1)"
[ -n "$CONTAINER" ] || die "Không thấy container 'srv-captain--${MYSQL_APP_NAME}'."

log "Restore '${MYSQL_DATABASE}' từ $(basename "$SRC_FILE")..."
gunzip -c "$SRC_FILE" | docker exec -i -e MYSQL_PWD="$MYSQL_PASSWORD" "$CONTAINER" \
    mysql --default-character-set=utf8mb4 -u "$MYSQL_USER" "$MYSQL_DATABASE"

log "HOÀN TẤT. Kiểm tra lại app + đăng nhập thử."
