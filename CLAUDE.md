# ncktrnr.com

Nick Turner's personal portfolio site. Drupal 11, custom Tailwind theme,
GreenGeeks shared hosting. Currently moving from v1.0 (work in progress) to
v2 based on new Figma designs.

Project notes, briefs and Nick's profile/preferences live in
`~/Projects/cc-websites/` – read `ncktrnr-v2-brief.md` there for goals and
status, and `ncktrnr-v2-design-build-plan.md` for the v2 build plan
(canonical Figma frames, milestones M0–M5, motion/parallax approach, agreed
decisions). Writing conventions (en dashes, UK spelling, sentence case) are
in that folder's CLAUDE.md and apply to all copy.

## Stack and layout

- Drupal 11.2, composer-managed, config sync in `config/sync/`
- Custom theme: `web/themes/custom/ncktrnr_tw` – Tailwind CSS v4 via CLI,
  templates in `templates/`, Lottie assets in `lottie/`
- Local dev: **ddev** (`ddev start`; migrated from Lando 2026-07-11 – if a
  `.lando.yml` is still around it is legacy)
- Local URL: http://ncktrnr.ddev.site (https works after `mkcert -install`)
- Design source: Figma file `ncktrnr.com`, key `8hxwpK5nd45OX5jC0OsGyN`

## Commands

```bash
ddev start / ddev stop
ddev drush <cmd>                                       # drush in the container
ddev drush uli                                         # admin login link
ddev npm --prefix web/themes/custom/ncktrnr_tw run watch      # Tailwind watch
ddev npm --prefix web/themes/custom/ncktrnr_tw run build:prod # minified build
scripts/pull-db.sh [--files]                           # refresh DB (and uploads) from prod
scripts/deploy.sh [--dry-run]                          # deploy main to production
```

## Workflow

Feature branch → PR on GitHub → merge to `main` → `scripts/deploy.sh`.
Config changes: make them in the admin UI locally, `ddev drush cex -y`,
commit the diff in `config/sync/` with the related code.

## Guardrails

- `web/sites/default/settings.php` is committed and credential-free; ddev and
  production each load their own settings file. Never commit or deploy
  `settings.ddev.php`, `settings.prod.php` or `settings.local.php`.
- Never edit production directly except the documented one-time steps in
  `docs/deployment.md` (which also covers the environment map and the
  blank-site checklist).
- Deploys only from a clean, pushed `main` – the script enforces this.
- SQL dumps and `backups/` are gitignored; never commit database dumps.
