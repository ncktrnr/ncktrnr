(function (Drupal, once) {
  'use strict';

  // Drupal behavior: builds the animated logo only once per element
  Drupal.behaviors.logoExpand = {
    attach(context) {
      const logos = once('logoExpand', '.js-logo', context);
      if (!logos.length) return;

      // Map acronym letters to their positions in the full string (left-to-right)
      function mapKept(abbr, full) {
        const kept = []; let j = 0;
        for (let i = 0; i < full.length && j < abbr.length; i++) {
          if (full[i].toLowerCase() === abbr[j].toLowerCase()) {
            kept.push({ fullIndex: i, abbrIndex: j });
            j++;
          }
        }
        return kept;
      }

      // Create an offscreen measurer where every character is its own span
      function spanify(str) {
        const el = document.createElement('span');
        Object.assign(el.style, {
          position: 'absolute',
          visibility: 'hidden',
          whiteSpace: 'nowrap',
          left: '-9999px',
          top: '-9999px'
        });
        for (let i = 0; i < str.length; i++) {
          const s = document.createElement('span');
          s.textContent = str[i];
          el.appendChild(s);
        }
        return el;
      }

      // Copy typography from the visible logo so we measure with exact metrics
      function copyType(to, fromEl) {
        const cs = getComputedStyle(fromEl);
        to.style.fontFamily = cs.fontFamily;
        to.style.fontSize = cs.fontSize;
        to.style.fontWeight = cs.fontWeight;
        to.style.letterSpacing = cs.letterSpacing;
        to.style.fontKerning = cs.fontKerning;
      }

      // Get left positions (relative to the wrapper) and wrapper width
      function getPositions(wrapper) {
        const spans = Array.from(wrapper.children);
        const base = wrapper.getBoundingClientRect();
        return {
          lefts: spans.map(s => s.getBoundingClientRect().left - base.left),
          width: base.width
        };
      }

      logos.forEach((logo) => {
        const track = logo.querySelector('.js-track');
        if (!track) return;

        // Allow override via data attributes, else default to "nick turner" / "ncktrnr"
        const fullStr = logo.getAttribute('data-full') || 'nick\u00A0turner'; // NBSP between names
        const abbrStr = logo.getAttribute('data-abbr') || 'ncktrnr';
        const keptMap = mapKept(abbrStr, fullStr);

        const measureFull = spanify(fullStr);
        const measureAbbr = spanify(abbrStr);
        document.body.appendChild(measureFull);
        document.body.appendChild(measureAbbr);

        let built = false;

        function build() {
          copyType(measureFull, logo);
          copyType(measureAbbr, logo);

          const full = getPositions(measureFull);
          const abbr = getPositions(measureAbbr);

          track.innerHTML = '';
          for (let i = 0; i < fullStr.length; i++) {
            const ch = fullStr[i];
            const span = document.createElement('span');
            span.className = 'char';
            span.textContent = ch;
            span.style.left = full.lefts[i] + 'px';

            const keptEntry = keptMap.find(k => k.fullIndex === i);
            if (keptEntry) {
              span.classList.add('keep');
              const dx = abbr.lefts[keptEntry.abbrIndex] - full.lefts[i];
              span.style.setProperty('--dx', dx + 'px');
            } else {
              span.classList.add('missing'); // hidden until hover/focus via CSS
            }
            track.appendChild(span);
          }

          // Update CSS custom properties for container width
          const abbrW = Math.ceil(getPositions(measureAbbr).width);
          const fullW = Math.ceil(getPositions(measureFull).width);
          logo.style.setProperty('--abbr-w', abbrW + 'px');
          logo.style.setProperty('--full-w', fullW + 'px');

          built = true;
        }

        // Rebuild when the logo box size/typography changes
        const ro = new ResizeObserver(() => { if (built) build(); });

        // Build when the logo first comes into view (saves work on SSR/offscreen)
        const io = new IntersectionObserver((entries) => {
          if (entries.some(e => e.isIntersecting)) {
            const ready = document.fonts && document.fonts.ready ? document.fonts.ready : Promise.resolve();
            ready.then(() => { build(); ro.observe(logo); });
            io.disconnect();
          }
        }, { rootMargin: '0px 0px 200px 0px' });
        io.observe(logo);

        // If fonts finish loading later (e.g., swap/variable axis), rebuild
        if (document.fonts) {
          if (document.fonts.addEventListener) {
            document.fonts.addEventListener('loadingdone', () => { if (built) build(); });
          }
          document.fonts.ready && document.fonts.ready.then(() => { if (built) build(); });
        }

        // Fallback: viewport resize debounce
        let t;
        window.addEventListener('resize', () => {
          clearTimeout(t);
          t = setTimeout(() => { if (built) build(); }, 120);
        });
      });
    }
  };
})(Drupal, once);
