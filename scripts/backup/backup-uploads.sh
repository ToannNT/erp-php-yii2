#!/usr/bin/env bash
#
# Backup thư mục ảnh upload (host) -> Cloudflare R2, incremental.
# Chạy TRÊN HOST bằng cron. Xem README.md.
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

log() { printf '[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*"; }
die() { log "LỖI: $*" >&2; exit 1; }

[ -f "$SCRIPT_DIR/backup.env" ] || die "Chưa có $SCRIPT_DIR/backup.env (copy từ backup.env.example)."
# shellcheck source=/dev/null
. "$SCRIPT_DIR/backup.env"

: "${UPLOADS_DIR:?Thiếu UPLOADS_DIR trong backup.env}"
: "${RCLONE_REMOTE:?Thiếu RCLONE_REMOTE}"
: "${R2_BUCKET:?Thiếu R2_BUCKET}"

command -v rclone >/dev/null || die "Không tìm thấy rclone (xem README để cài)."
[ -d "$UPLOADS_DIR" ] || die "Không thấy thư mục $UPLOADS_DIR."

# Số file nhiều nhất được phép xoá trên R2 trong MỘT lần chạy.
: "${MAX_DELETE:=50}"

# GUARD 1 — thư mục rỗng.
# Nếu bind mount hỏng (hoặc Persistent Directory chưa gắn), $UPLOADS_DIR sẽ rỗng và
# `rclone sync` hiểu là "user đã xoá hết" -> xoá sạch backup trên R2.
# Rỗng thì không có gì để backup, nên bỏ qua luôn: giữ nguyên bản trên R2, an toàn cho cả 2 tình huống.
if [ -z "$(find "$UPLOADS_DIR" -type f -print -quit)" ]; then
    log "CẢNH BÁO: $UPLOADS_DIR không có file nào — BỎ QUA sync để không xoá nhầm backup trên R2."
    log "Nếu đây không phải site mới, kiểm tra ngay xem mount còn hoạt động không."
    exit 0
fi

# GUARD 2 — --max-delete.
# Xoá lai rai vài ảnh là bình thường; xoá hàng loạt là dấu hiệu sự cố.
# Vượt ngưỡng thì rclone huỷ toàn bộ thao tác (không xoá gì) và thoát khác 0.
log "Sync $UPLOADS_DIR -> ${RCLONE_REMOTE}:${R2_BUCKET}/uploads/ (max-delete=${MAX_DELETE}) ..."
if ! rclone sync "$UPLOADS_DIR" "${RCLONE_REMOTE}:${R2_BUCKET}/uploads/" \
    --max-delete "$MAX_DELETE" \
    --s3-no-check-bucket \
    --fast-list \
    --transfers 8 \
    --stats-one-line; then
    die "Sync thất bại. Nếu do vượt --max-delete=${MAX_DELETE}: rclone đã HUỶ, backup trên R2 còn nguyên.
     Kiểm tra vì sao nhiều file biến mất. Nếu việc xoá là đúng ý, chạy lại:  MAX_DELETE=<số lớn hơn> $0"
fi

log "HOÀN TẤT."
