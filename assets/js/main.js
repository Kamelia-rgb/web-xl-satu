(function () {
  'use strict';

  // ---------- Menu HP ----------
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.getElementById('menu-utama');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  // ---------- Slider banner promo ----------
  document.querySelectorAll('[data-slider]').forEach(function (slider) {
    var track = slider.querySelector('.slider-track');
    var slides = track ? track.children : [];
    var dotsWrap = slider.querySelector('.slider-dots');
    if (!track || slides.length < 2) return;

    var index = 0;
    var dots = [];
    for (var i = 0; i < slides.length; i++) {
      (function (n) {
        var b = document.createElement('button');
        b.type = 'button';
        b.setAttribute('aria-label', 'Banner ' + (n + 1));
        b.addEventListener('click', function () { go(n); restart(); });
        dotsWrap.appendChild(b);
        dots.push(b);
      })(i);
    }

    function mark(n) {
      index = n;
      dots.forEach(function (d, k) { d.setAttribute('aria-current', k === n ? 'true' : 'false'); });
    }
    function go(n) {
      n = (n + slides.length) % slides.length;
      track.scrollTo({ left: slides[n].offsetLeft - track.offsetLeft, behavior: 'smooth' });
      mark(n);
    }

    var prev = slider.querySelector('.slider-nav.prev');
    var next = slider.querySelector('.slider-nav.next');
    if (prev) prev.addEventListener('click', function () { go(index - 1); restart(); });
    if (next) next.addEventListener('click', function () { go(index + 1); restart(); });

    var scrollTimer;
    track.addEventListener('scroll', function () {
      clearTimeout(scrollTimer);
      scrollTimer = setTimeout(function () {
        mark(Math.round(track.scrollLeft / track.clientWidth));
      }, 80);
    });

    var timer;
    function restart() {
      clearInterval(timer);
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
      timer = setInterval(function () { go(index + 1); }, 5000);
    }
    slider.addEventListener('mouseenter', function () { clearInterval(timer); });
    slider.addEventListener('mouseleave', restart);
    mark(0);
    restart();
  });

  // ---------- Pelacakan klik WhatsApp (Google Ads / Meta Pixel) ----------
  document.addEventListener('click', function (ev) {
    var link = ev.target.closest('[data-track="wa"]');
    if (!link) return;
    var label = link.getAttribute('data-paket') || '';
    if (typeof window.gtag === 'function') {
      window.gtag('event', 'klik_whatsapp', { paket: label });
      if (window.SITE && window.SITE.adsConversion) {
        window.gtag('event', 'conversion', { send_to: window.SITE.adsConversion });
      }
    }
    if (typeof window.fbq === 'function') {
      window.fbq('track', 'Contact', { content_name: label });
    }
  });
})();

// ---------- Filter kategori paket ----------
document.querySelectorAll('[data-pkg-filter]').forEach(function (wrap) {
  var buttons = wrap.querySelectorAll('[data-filter]');
  var cards = wrap.querySelectorAll('.pkg');
  buttons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var f = btn.getAttribute('data-filter');
      buttons.forEach(function (b) { b.setAttribute('aria-selected', b === btn ? 'true' : 'false'); });
      cards.forEach(function (c) { c.hidden = !!f && c.getAttribute('data-kategori') !== f; });
    });
  });
});

// ---------- Animasi: muncul saat digulir ----------
(function () {
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var selectors = '.section-head, .feature, .pkg, .steps li, .faq details, .cta-band, .promo-card, .slider, .why-item, .perks, .sb-card, .myxl-text, .hero-offer, .hero-offer-meta';
  var els = Array.prototype.slice.call(document.querySelectorAll(selectors));
  els.forEach(function (el) {
    el.classList.add('reveal');
    // Muncul bergantian untuk elemen yang berdampingan
    var siblings = el.parentElement ? Array.prototype.indexOf.call(el.parentElement.children, el) : 0;
    el.style.setProperty('--d', Math.min(siblings, 5) * 0.08 + 's');
  });
  if (reduce || !('IntersectionObserver' in window)) {
    els.forEach(function (el) { el.classList.add('is-visible'); });
    return;
  }
  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (en) {
      if (!en.isIntersecting) return;
      var el = en.target;
      el.classList.add('is-visible');
      io.unobserve(el);
      // Bersihkan setelah animasi selesai supaya efek hover kartu tetap jalan
      setTimeout(function () { el.classList.remove('reveal', 'is-visible'); el.style.removeProperty('--d'); }, 1200);
    });
  }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
  els.forEach(function (el) { io.observe(el); });
})();

// ---------- Ikon myXL berputar mengikuti guliran + bar daftar di HP ----------
(function () {
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var section = document.querySelector('[data-orbit-section]');
  var orbit = section && section.querySelector('.orbit');
  var bar = document.querySelector('.sticky-cta');
  var footer = document.querySelector('.site-footer');
  var ticking = false;

  function update() {
    ticking = false;
    var vh = window.innerHeight;
    if (orbit && !reduce) {
      var r = section.getBoundingClientRect();
      if (r.bottom > 0 && r.top < vh) {
        // 0 saat bagian baru masuk layar, 1 saat sudah lewat
        var progress = (vh - r.top) / (vh + r.height);
        orbit.style.setProperty('--rot', (progress * 200 - 60).toFixed(2) + 'deg');
      }
    }
    if (bar) {
      var nearFooter = footer && footer.getBoundingClientRect().top < vh;
      var show = window.scrollY > 500 && !nearFooter;
      bar.classList.toggle('show', show);
      bar.setAttribute('aria-hidden', show ? 'false' : 'true');
      var link = bar.querySelector('a');
      if (link) link.tabIndex = show ? 0 : -1;
      document.body.classList.toggle('cta-on', show);
    }
  }
  function onScroll() {
    if (!ticking) { ticking = true; window.requestAnimationFrame(update); }
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onScroll);
  update();
})();
