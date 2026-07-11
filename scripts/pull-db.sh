#!/usr/bin/env bash
#
# Pull the production database (and optionally uploaded files) into ddev.
#
# Usage:
#   scripts/pull-db.sh            pull and import the production database
#   scripts/pull-db.sh --files    also pull sites/default/files (uploads)

set -euo pipefail
cd "$(dirname "$0")/.."

REMOTE="greengeeks-fairytales"
REMOTE_HOME="/home/fairytal"
REMOTE_DOCROOT="$REMOTE_HOME/public_html/ncktrnr.com"
REMOTE_DRUSH="$REMOTE_HOME/ncktrnr/vendor/bin/drush --root=$REMOTE_DOCROOT"

STAMP=$(date +%Y%m%d-%H%M)
DUMP="backups/prod-$STAMP.sql.gz"
mkdir -p backups

echo "==> Making sure ddev is running"
ddev start -y >/dev/null

echo "==> Dumping production database"
ssh "$REMOTE" "$REMOTE_DRUSH sql:dump" | gzip > "$DUMP"
[[ -s "$DUMP" ]] || { echo "FAIL: dump is empty"; exit 1; }
echo "    saved $DUMP ($(du -h "$DUMP" | cut -f1))"

echo "==> Importing into ddev"
# Piped through the mysql client directly – `ddev import-db` has silently
# truncated imports of these MariaDB 11.4 dumps partway through.
ddev mysql -e "DROP DATABASE IF EXISTS db; CREATE DATABASE db"
zcat < "$DUMP" | ddev mysql db

echo "==> Applying any pending local updates"
ddev drush updb -y
ddev drush cr

if [[ "${1:-}" == "--files" ]]; then
  echo "==> Pulling sites/default/files (no deletions locally)"
  rsync -rlptz "$REMOTE:$REMOTE_DOCROOT/sites/default/files/" web/sites/default/files/
fi

echo "==> Done – local site now mirrors production content ($DUMP kept as backup)"
