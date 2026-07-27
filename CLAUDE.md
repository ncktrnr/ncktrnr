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

## Theme conventions (v2, July 2026)

- Tailwind utilities in templates over custom CSS; custom CSS only with a
  stated justification (see the hero block in `css/tailwind.css`). Round
  Figma values to the nearest stock utility – Figma is a starting point.
- Standard heading sizes for h2/h3 live in `@layer base`; editor-entered
  field markup is styled from templates with `[&_…]` descendant utilities.
- SVG assets are SVGO'd on the way into the theme (lossless minification,
  viewBox kept): `npx svgo --multipass -i in.svg -o images/…/out.svg`.
- Homepage copy lives in fields on the Home node (field_intro,
  field_columns, field_connect, field_content = columns heading);
  `page--front.html.twig` owns the front-page layout.
- Scroll-driven CSS animations (hero parallax) do not run in Claude's
  embedded preview pane – verify in a real browser. Inside the
  overflow-hidden hero, use `scroll(root)`, never bare `scroll()`.
- **Spacing direction: use `margin-bottom` and `gap`, never `margin-top`.**
  Where a child needs pushing to the bottom of a flex column, use `grow` on
  the element above it rather than `mt-auto` on the element itself.
- The reading measure belongs to the **text**, not the page. Basic pages
  compose their layout in Layout Builder, so the node template cannot cap the
  width – `ncktrnr_tw_preprocess_field()` puts `prose` on the fields that
  carry long-form text (`field_content`, block `body`). Page titles never take
  the measure: every `h1` starts at the container's left edge, so it does not
  shift sideways between a prose page and the work page's card grid.
- Layout Builder renders its field blocks with `#view_mode` of **`_custom`**,
  not `full`. Anything keyed off the view mode has to allow both, or every
  Basic page silently drops out.
- `prose` zeroes the bottom margin of its last child, so spacing below a
  heading that is alone inside a prose wrapper has to sit on the wrapper.
- The `3_2_*` media displays crop **server-side** via `focal_point_scale_and_crop`,
  which centres when no focal point is stored. `object-position` in a template
  is a no-op against them – set a focal point on the media instead.
- Shared responsive image styles (`3_2_wide` and friends) declare
  `sizes: 100vw`, which makes every browser fetch the 1800w rung. Work cards
  use `3_2_card` instead – same rungs, honest `sizes`. Give any new fixed-width
  component its own style rather than editing the shared ones.
- Card geometry on `/work` must not vary with image count: frame width and
  strip padding are unconditional, because width drives height on a 3:2 box and
  one taller card knocks its neighbour's title out of line.
- Drupal's `visually-hidden` field labels are absolutely positioned and will
  escape an `overflow-x-auto` scroller unless that scroller is `relative`,
  stretching the document sideways in Chrome (not Safari).
- Preview on `ncktrnr.ddev.site`: the Browser pane allows `javascript_tool`
  and `resize_window` but blocks screenshots. Measure the DOM in the pane;
  screenshot via headless Chrome – which does **not** apply a mobile viewport,
  so narrow captures look broken when they are fine.
- For a dark-mode capture pass headless Chrome `--force-dark-mode` **alone**.
  Adding `--enable-features=WebContentsForceDark` turns on Chrome's auto-dark,
  which inverts the page and yields a confidently wrong screenshot.
- The committed `css/styles.css` is the unminified `npm run dev` output, but
  `deploy.sh` runs `build:prod`, which minifies it in place. **Every deploy
  leaves the working tree dirty** – run `npm run dev` afterwards to restore it.
- `@tailwindcss/typography` colours prose through its own `--tw-prose-*`
  variables, set on `.prose` itself, so they beat anything inherited from
  `body`. They are mapped to the design tokens in `css/tailwind.css`. Don't
  reach for `dark:prose-invert`: the `dark` variant here matches only the
  manual `[data-theme]` override, so it misses system dark with no stored
  choice.
