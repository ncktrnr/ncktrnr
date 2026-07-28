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
  so narrow captures look broken when they are fine. The pane is also no use
  for anything animated: its compositor can freeze mid-session and stop
  responding to `javascript_tool` altogether.
- **Headless Chrome clamps `--window-size` to a 500px floor.** That is the
  mechanism behind the note above – the screenshot is cropped to the width you
  asked for while the page lays out at 500, so the nav appears to overflow when
  it does not. For a true mobile viewport, drive Chrome over CDP and call
  `Emulation.setDeviceMetricsOverride`; that also gives real wall-clock timing,
  media emulation (`prefers-reduced-motion`, `prefers-color-scheme`) and real
  pointer events for `:hover`. There is a working harness in
  `~/Projects/cc-websites/tools/cdp.mjs`.
- **`getComputedStyle` lies about a transitioning property under
  `--virtual-time-budget`.** The animation clock stays at 0 while
  `document.timeline` advances, so a running transition reads as its start
  value forever. Read a non-transitioning element instead, or call
  `el.getAnimations().forEach(a => a.finish())` first. This cost an hour of
  chasing a phantom bug in CSS that was correct.
- Give each headless run its own `--user-data-dir` and delete it afterwards.
  A reused profile carries `localStorage` between runs, which silently
  pre-seeds any test meant to start with nothing stored.
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
- **A template class change needs `npm run dev`, not just `drush cr`.**
  Tailwind scans the templates to decide which utilities to emit, so a class
  added to Twig simply does not exist in the CSS until the build runs – and
  the symptom is a rule that silently does nothing.

## Motion (M4, July 2026)

- **One gate for everything.** Tailwind's built-in `motion-safe` and
  `motion-reduce` variants are redefined in `css/tailwind.css` with
  `@custom-variant`, keyed off `data-motion` on `<html>`: absent = follow the
  OS, `reduced` = off regardless, `full` = on regardless. Utilities in
  templates get this for free; hand-written CSS opts in with v4's `@variant`
  at-rule (`.thing { @variant motion-safe { … } }`). Don't add a third variant
  name or a fresh `prefers-reduced-motion` media query.
- **JS that cannot be gated by CSS reads the answer, it does not work it out.**
  `js/motion-toggle.js` publishes `data-motion-resolved` on `<html>` ('full' or
  'reduced', OS and stored choice already reconciled) and fires
  `ncktrnr:motionchange` on `document` when it changes, with the same value in
  `detail.motion`. The attribute is for the initial read – no event fires on
  load. `js/lottie-motion.js` is the worked example; the video lead items will
  want the same pair.
- **`.reveal` belongs to the logo's expanding mask** (`.logo .reveal`). Scroll
  reveals are `.rise` / `.rise-stagger`. A generic `.reveal` landed straight on
  the logo and was invisible in screenshots – check what a new class name
  already matches before using it.
- **Reveals must not be able to hide content.** `.rise`'s hidden state exists
  only as the keyframe's `from`, held by `animation-fill-mode: both`, and the
  whole rule sits inside `@supports (animation-timeline: view())`. Never add a
  standalone `opacity: 0` – without support, or with motion off, nothing should
  be set at all and the finished page simply shows.
- Scroll-driven animations need an `animation-range`. With the range left at
  `normal` a `scroll(root)` timeline spends its distance over the whole
  document, so the effect weakens as the page grows and differs per device.
- The day/night crossfade transitions **registered** `--tone-*` properties on
  `:root`; an unregistered custom property is a string and can only flip.
  `--tone-line` and `--tone-rust-tint` are deliberately left unregistered –
  they are `color-mix()` over other tokens and re-substitute their `var()`
  references every frame, so they follow along for free.
- Crossfading a theme means text and background swap, so they must cross:
  body text passes through about **1.1:1** mid-fade. The trough is geometric –
  its depth does not change with duration, only how long it lasts. Fading the
  pair separately is worse, not better.
- `lottie-player` freezes itself when scrolled out of view, so
  `currentState: "frozen"` for the footer dino is normal, not a fault. Its
  `autoplay`/`loop` are HTML attributes read at upgrade time and are therefore
  kept **off** the markup – `js/lottie-motion.js` starts it. `hover` was inert
  alongside `autoplay` and would have come alive when autoplay was removed.
