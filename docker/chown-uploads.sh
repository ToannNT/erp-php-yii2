#!/bin/sh
# Chạy lúc container start (nginx/unit entrypoint tự exec /docker-entrypoint.d/*.sh bằng root).
# Persistent dir / bind mount thường được tạo với owner root -> PHP (chạy bằng user `unit`) không ghi được.
# Fix quyền để upload local hoạt động. Idempotent, rẻ.
chown -R unit:unit /app/api/web/uploads 2>/dev/null || true
