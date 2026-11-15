/* Janwar Mart: small enhancements only. Every page also works without JavaScript. */
(function () {
  var $ = function (s, r) { return (r || document).querySelector(s); }, $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var csrf = ($('meta[name=csrf-token]') || {}).content || '';

  /* theme (light / dark), remembered in this browser */
  var thb = $('#thb');
  function paintTheme() { if (thb) thb.textContent = document.documentElement.dataset.theme === 'dark' ? '☀' : '🌙'; }
  paintTheme();
  if (thb) thb.addEventListener('click', function () {
    var n = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
    document.documentElement.dataset.theme = n; try { localStorage.setItem('jm_theme', n); } catch (e) {} paintTheme();
  });

  /* scroll reveal (content is shown even if the observer is missing) */
  var rv = $$('.rv');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }); }, { threshold: .08 });
    rv.forEach(function (e) { io.observe(e); });
    setTimeout(function () { rv.forEach(function (e) { e.classList.add('in'); }); }, 1800);
  } else rv.forEach(function (e) { e.classList.add('in'); });

  /* voice search */
  var mic = $('#mic'), inp = $('#hs');
  if (mic) mic.addEventListener('click', function () {
    var SR = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SR) { alert(document.documentElement.lang === 'ur' ? 'اس براؤزر میں آواز سے تلاش نہیں چلتی۔' : 'Voice input is not supported in this browser.'); return; }
    var r = new SR(); r.lang = document.documentElement.lang === 'ur' ? 'ur-PK' : 'en-US';
    mic.classList.add('rec'); mic.setAttribute('aria-pressed', 'true');
    r.onresult = function (e) { inp.value = e.results[0][0].transcript; inp.form.submit(); };
    r.onend = r.onerror = function () { mic.classList.remove('rec'); mic.setAttribute('aria-pressed', 'false'); };
    r.start();
  });

  /* phone OTP buttons:  <button data-otp="signup|reset" data-phone="#phone"> */
  $$('[data-otp]').forEach(function (b) {
    b.addEventListener('click', function () {
      var ph = $(b.dataset.phone), msg = $(b.dataset.msg || '#otpmsg'); if (!ph) return;
      b.disabled = true;
      fetch('/otp/send', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }, body: JSON.stringify({ phone: ph.value, purpose: b.dataset.otp }) })
        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
        .then(function (x) {
          if (msg) { msg.textContent = x.ok ? (b.dataset.sent || 'Code sent') : (x.j.error || (x.j.errors && x.j.errors.phone && x.j.errors.phone[0]) || 'Error'); msg.className = x.ok ? '' : 'err'; }
          if (x.ok) { var s = 60, t = b.textContent; var iv = setInterval(function () { b.textContent = (--s) + 's'; if (s <= 0) { clearInterval(iv); b.textContent = t; b.disabled = false; } }, 1000); }
          else b.disabled = false;
        }).catch(function () { b.disabled = false; });
    });
  });

  /* sell wizard: sections with data-step */
  var steps = $$('[data-step]');
  if (steps.length) {
    var cur = 0, bar = $$('.steps i');
    function show(i) { cur = i; steps.forEach(function (s, k) { s.classList.toggle('hidden-step', k !== i); }); bar.forEach(function (b, k) { b.classList.toggle('d', k <= i); }); window.scrollTo({ top: 0, behavior: 'smooth' }); }
    $$('[data-next]').forEach(function (b) { b.addEventListener('click', function () {
      var ok = true; $$('input,select,textarea', steps[cur]).forEach(function (f) { if (!f.checkValidity()) { f.reportValidity(); ok = false; } });
      if (ok && cur < steps.length - 1) show(cur + 1); }); });
    $$('[data-prev]').forEach(function (b) { b.addEventListener('click', function () { if (cur > 0) show(cur - 1); }); });
    var ph = $('#photos'), pv = $('#photoPrev');
    if (ph && pv) ph.addEventListener('change', function () { pv.innerHTML = ''; Array.prototype.slice.call(ph.files, 0, 4).forEach(function (f) { var i = document.createElement('img'); i.src = URL.createObjectURL(f); pv.appendChild(i); }); });
    show(0);
  }

  /* detail page: click a thumbnail to enlarge */
  $$('.thumbs img').forEach(function (t) { t.addEventListener('click', function () { var m = $('#mainImg'); if (m) m.src = t.src; }); });

  /* PWA: service worker + install button */
  if ('serviceWorker' in navigator) window.addEventListener('load', function () { navigator.serviceWorker.register('/sw.js').catch(function () {}); });
  var dp, ib = $('#installBtn');
  window.addEventListener('beforeinstallprompt', function (e) { e.preventDefault(); dp = e; if (ib) ib.hidden = false; });
  if (ib) ib.addEventListener('click', function (e) { e.preventDefault(); if (dp) { dp.prompt(); dp = null; ib.hidden = true; } });
})();
