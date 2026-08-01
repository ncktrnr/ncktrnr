/**
 * Holds looping videos to the motion preference, and plays them only in view.
 *
 * Same shape as js/lottie-motion.js, and for the same reason: `autoplay` is an
 * HTML attribute read at parse time, so no stylesheet can stop it. It is left
 * off the markup and the decision is made here instead – with motion off, the
 * loop never starts and the poster stands, which is the whole point of the
 * poster being required.
 *
 * The preference is read, never re-derived: `data-motion-resolved` on <html>
 * for the current answer and `ncktrnr:motionchange` for when it changes, both
 * owned by js/motion-toggle.js.
 *
 * Generic on purpose. Any <video class="js-video-loop"> gets this, so the
 * gallery and the card lead items share one implementation.
 */
(function () {
  'use strict';

  var videos = document.querySelectorAll('video.js-video-loop');
  if (!videos.length) {
    return;
  }

  var inView = new WeakSet();

  function allowed() {
    // Absent means motion-toggle.js has not run yet. Nothing starts until it
    // has, and the change event picks the videos up when it does.
    return document.documentElement.dataset.motionResolved === 'full';
  }

  function apply(video) {
    if (allowed() && inView.has(video)) {
      // Muted is what makes programmatic play() permissible; the attribute
      // says so too, but a stale DOM state should not silently block it.
      video.muted = true;
      var playing = video.play();
      if (playing && playing.catch) {
        // A rejected play() is normal – a background tab, or a decode the
        // browser declined. The poster stays, which is a correct resting state.
        playing.catch(function () {});
      }
    } else {
      video.pause();
      // Back to the poster frame rather than wherever the loop was
      // interrupted, matching how the Lottie players rest.
      video.currentTime = 0;
    }
  }

  function applyAll() {
    Array.prototype.forEach.call(videos, apply);
  }

  // Browsers throttle offscreen video inconsistently rather than stopping it,
  // so playback is driven explicitly. rootMargin lets a tile spin up just
  // before it arrives instead of visibly starting from a standstill.
  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        inView.add(entry.target);
      } else {
        inView.delete(entry.target);
      }
      apply(entry.target);
    });
  }, { rootMargin: '100px' });

  Array.prototype.forEach.call(videos, function (video) {
    observer.observe(video);
  });

  document.addEventListener('ncktrnr:motionchange', applyAll);
})();
