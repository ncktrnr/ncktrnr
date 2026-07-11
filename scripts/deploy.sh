#!/usr/bin/env bash
#
# Deploy ncktrnr.com to GreenGeeks shared hosting.
#
# Usage:
#   scripts/deploy.sh            full deploy (build, rsync, drush deploy, verify)
#   scripts/deploy.sh --dry-run  show what rsync would transfer, change nothing
#
# What it never touches on the server (excluded and protected from --delete):
#   - public_html/ncktrnr.com/autoload.php        (points at ../../ncktrnr/vendor)
#   - sites/default/settings.php                  (server keeps its own)
#   - sites/default/settings.prod.php             (server-only, never in git)
#   - sites/default/settings.local.php            (must not exist on server)
#   - sites/*/files                               (user uploads)
#   - .well-known, cgi-bin                        (cPanel/AutoSSL territory)

set -euo pipefail
cd "$(dirname "$0")/.."

# ---------------------------------------------------------------- config ----
REMOTE="greengeeks-fairytales"                       # ssh alias in ~/.ssh/config
REMOTE_HOME="/home/fairytal"
REMOTE_DOCROOT="$REMOTE_HOME/public_html/ncktrnr.com"
REMOTE_VENDOR="$REMOTE_HOME/ncktrnr/vendor"
REMOTE_CONFIG="$REMOTE_HOME/config/ncktrnr/sync"
REMOTE_DRUSH="$REMOTE_VENDOR/bin/drush --root=$REMOTE_DOCROOT"
SITE_URL="https://ncktrnr.com"

DRY_RUN=""
[[ "${1:-}" == "--dry-run" ]] && DRY_RUN="--dry-run"

log()  { printf '\n\033[1;34m==> %s\033[0m\n' "$*"; }
fail() { printf '\n\033[1;31mFAIL: %s\033[0m\n' "$*"; exit 1; }

# ------------------------------------------------------------- preflight ----
log "Preflight checks"

[[ "$(git branch --show-current)" == "main" ]] \
  || fail "not on main (deploy only what has been through a PR)"

[[ -z "$(git status --porcelain)" ]] \
  || fail "working tree not clean – commit or stash first"

git fetch origin main --quiet
[[ "$(git rev-parse HEAD)" == "$(git rev-parse origin/main)" ]] \
  || fail "local main is not in sync with origin/main – pull or push first"

ssh "$REMOTE" "test -L $REMOTE_HOME/ncktrnr/web" \
  || fail "server symlink $REMOTE_HOME/ncktrnr/web is missing"

# ----------------------------------------------------------------- build ----
if [[ -z "$DRY_RUN" ]]; then
  log "Building production vendor (composer --no-dev)"
  ddev composer install --no-dev --optimize-autoloader

  log "Building theme CSS"
  ddev npm --prefix web/themes/custom/ncktrnr_tw run build:prod
else
  log "Dry run – skipping build, comparing current local state"
fi

# ----------------------------------------------------------------- rsync ----
log "Syncing vendor/ -> $REMOTE_VENDOR/"
rsync -rlptzv $DRY_RUN --delete \
  vendor/ "$REMOTE:$REMOTE_VENDOR/"

log "Syncing web/ -> $REMOTE_DOCROOT/"
rsync -rlptzv $DRY_RUN --delete \
  --exclude='/autoload.php' \
  --exclude='/sites/default/settings.php' \
  --exclude='/sites/default/settings.*.php' \
  --exclude='/sites/default/settings.local.php' \
  --exclude='/sites/*/files' \
  --exclude='/.well-known' \
  --exclude='/cgi-bin' \
  --exclude='/.ftpquota' \
  --exclude='/demos' \
  --exclude='node_modules' \
  --exclude='error_log' \
  --exclude='.DS_Store' \
  web/ "$REMOTE:$REMOTE_DOCROOT/"

log "Syncing config/sync/ -> $REMOTE_CONFIG/"
rsync -rlptzv $DRY_RUN --delete \
  config/sync/ "$REMOTE:$REMOTE_CONFIG/"

if [[ -n "$DRY_RUN" ]]; then
  log "Dry run complete – nothing changed on the server"
  exit 0
fi

# ------------------------------------------------------------ post-deploy ----
log "Running database updates, config import and cache rebuild on the server"
ssh "$REMOTE" "$REMOTE_DRUSH deploy -y"

# ------------------------------------------------------------ verification ----
log "Verifying the known failure modes"

ssh "$REMOTE" "grep -q 'ncktrnr/vendor/autoload.php' $REMOTE_DOCROOT/autoload.php" \
  || fail "autoload.php no longer points at $REMOTE_HOME/ncktrnr/vendor"

ssh "$REMOTE" "grep -q \"host.*localhost\" $REMOTE_DOCROOT/sites/default/settings.prod.php 2>/dev/null || ! grep -q \"'host' => 'db'\" $REMOTE_DOCROOT/sites/default/settings.php" \
  || fail "settings.php on the server appears to contain the local DB host"

ssh "$REMOTE" "test ! -e $REMOTE_DOCROOT/sites/default/settings.local.php" \
  || fail "settings.local.php exists on the server – remove it"

ssh "$REMOTE" "$REMOTE_DRUSH status --field=bootstrap | grep -q Successful" \
  || fail "drush cannot bootstrap the production site"

HTTP_CODE=$(curl -sS -o /dev/null -w '%{http_code}' "$SITE_URL")
[[ "$HTTP_CODE" == "200" ]] || fail "$SITE_URL returned HTTP $HTTP_CODE"

# ---------------------------------------------------------------- restore ----
log "Restoring local dev dependencies"
ddev composer install

log "Deploy complete – $SITE_URL is up (HTTP 200)"
