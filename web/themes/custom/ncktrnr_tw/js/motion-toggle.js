/**
 * Reduced-motion toggle. No stored choice = follow prefers-reduced-motion;
 * a click stores the opposite of whatever is currently resolved. The early
 * no-flash init lives inline in html.html.twig – it matters more here than it
 * does for colour, since without it a page starts animating and then stops.
 *
 * The CSS does all the gating (the motion-safe / motion-reduce variants in
 * tailwind.css), including the icon swap. This file only owns the preference,
 * for the benefit of anything CSS cannot gate – `<video autoplay>` and the
 * Lottie player, whose autoplay and loop are HTML attributes.
 *
 * The contract for those subscribers, so that none of them has to re-resolve
 * the preference for itself:
 *
 * - `data-motion-resolved` on <html> is the answer – 'full' or 'reduced' –
 *   with the stored choice and the OS setting already reconciled. Read it at
 *   any time, including on first run.
 * - `ncktrnr:motionchange` on `document` says when that answer changed, and
 *   repeats it in `detail.motion`. It does not fire on load.
 *
 * Two attributes rather than one because they answer different questions:
 * `data-motion` is what the person chose (and may be absent, meaning 'follow
 * the OS'), which is what the CSS variants key off; `data-motion-resolved` is
 * what that works out to right now.
 */
(function () {
  'use strict';

  var root = document.documentElement;
  var systemReduce = window.matchMedia('(prefers-reduced-motion: reduce)');
  var button = document.querySelector('.js-motion-toggle');
  var announced;

  /** The state the page is actually in: manual override, else the OS. */
  function resolved() {
    var stored = root.dataset.motion;
    if (stored === 'full' || stored === 'reduced') {
      return stored;
    }
    return systemReduce.matches ? 'reduced' : 'full';
  }

  /**
   * Publish the resolved state, and say so if it moved. Announcing only on a
   * real change means subscribers can treat every event as a change; the
   * attribute is what they read for the initial value, since no event fires
   * on load.
   */
  function sync() {
    var now = resolved();

    root.dataset.motionResolved = now;

    if (button) {
      // The label names the action, not the state – it is the accessible name.
      button.setAttribute('aria-label',
        now === 'full' ? 'Turn animation off' : 'Turn animation on');
    }

    if (now !== announced) {
      announced = now;
      document.dispatchEvent(new CustomEvent('ncktrnr:motionchange', {
        detail: { motion: now },
      }));
    }
  }

  if (button) {
    button.addEventListener('click', function () {
      root.dataset.motion = resolved() === 'full' ? 'reduced' : 'full';
      try {
        localStorage.setItem('motion', root.dataset.motion);
      } catch (e) {
        // Embedded previews can block storage – the toggle still works for
        // the current page, the choice just won't persist.
      }
      sync();
    });
  }

  // Changing the OS setting mid-visit only moves the page for someone with no
  // manual override; sync() decides whether that counts as a change.
  systemReduce.addEventListener('change', sync);

  // Seeded before the first sync so that publishing the initial state does not
  // announce it as a change.
  announced = resolved();
  sync();
})();
