/**
 * Holds the Lottie players to the motion preference.
 *
 * This is the one piece of motion the CSS gate cannot reach: `autoplay` and
 * `loop` are HTML attributes on <lottie-player>, read by the player when it
 * upgrades, so no stylesheet can stop it. Both are therefore left off the
 * markup and the decision is made here instead – the template no longer
 * asserts that the animation always runs.
 *
 * The preference is read, never re-derived: `data-motion-resolved` on <html>
 * for the current answer and `ncktrnr:motionchange` for when it changes, both
 * owned by js/motion-toggle.js.
 *
 * Stopped rather than paused, so the resting pose is frame 0 every time
 * instead of wherever the loop happened to be interrupted.
 */
(function () {
  'use strict';

  var players = document.querySelectorAll('lottie-player');
  if (!players.length) {
    return;
  }

  function allowed() {
    // Absent means motion-toggle.js has not run yet; the players are not ready
    // at that point either, and the `ready` handler below picks it up.
    return document.documentElement.dataset.motionResolved === 'full';
  }

  function apply(player) {
    try {
      if (allowed()) {
        player.setLooping(true);
        player.play();
      } else {
        player.stop();
      }
    } catch (e) {
      // Player not upgraded yet, or the CDN script never arrived – nothing to
      // drive, and nothing that should break the rest of the page.
    }
  }

  function applyAll() {
    Array.prototype.forEach.call(players, apply);
  }

  Array.prototype.forEach.call(players, function (player) {
    // The player upgrades asynchronously – the CDN script is deferred and the
    // animation JSON is fetched after that – so the first call has to wait.
    player.addEventListener('ready', function () {
      apply(player);
    });
  });

  document.addEventListener('ncktrnr:motionchange', applyAll);

  // Covers a player that became ready before this file ran.
  applyAll();
})();
