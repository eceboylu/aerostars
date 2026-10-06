(function () {
  "use strict";

  var reduce = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  // Mobile menu
  var toggle = document.querySelector(".nav-toggle");
  var nav = document.getElementById("nav");
  if (toggle && nav) {
    // Items slide in one after another when the menu opens
    Array.prototype.forEach.call(nav.children, function (item, i) {
      item.style.setProperty("--d", (i * 55) + "ms");
    });
    var setMenu = function (open) {
      toggle.setAttribute("aria-expanded", String(open));
      toggle.textContent = open ? "Close" : "Menu";
      nav.classList.toggle("is-open", open);
    };
    toggle.addEventListener("click", function () {
      setMenu(toggle.getAttribute("aria-expanded") !== "true");
    });
    // Escape and a tap on any link close the menu
    document.addEventListener("keydown", function (ev) {
      if (ev.key === "Escape" && nav.classList.contains("is-open")) { setMenu(false); toggle.focus(); }
    });
    nav.addEventListener("click", function (ev) {
      if (ev.target.closest && ev.target.closest("a") && nav.classList.contains("is-open")) setMenu(false);
    });
  }

  // Category filter (all categories remain visible without JavaScript)
  var chips = document.querySelectorAll("[data-filter]");
  var blocks = document.querySelectorAll("[data-family]");
  chips.forEach(function (chip) {
    chip.addEventListener("click", function () {
      var want = chip.getAttribute("data-filter");
      chips.forEach(function (c) { c.setAttribute("aria-pressed", String(c === chip)); });
      blocks.forEach(function (b) {
        b.hidden = !(want === "all" || b.getAttribute("data-family") === want);
      });
      // Re-run the reveal on the blocks that just appeared
      blocks.forEach(function (b) { if (!b.hidden) b.querySelectorAll(".reveal").forEach(function (el) { el.classList.add("is-in"); }); });
    });
  });

  // Entry form
  var form = document.getElementById("entry-form");
  if (form) {
    var boxes = form.querySelectorAll('input[name="categories[]"]');
    var picked = document.getElementById("picked");
    var update = function () {
      var n = 0;
      boxes.forEach(function (b) { if (b.checked) n++; });
      if (picked) picked.textContent = n === 0 ? "No categories selected yet" : n + (n === 1 ? " category selected" : " categories selected");
    };
    var wanted = new URLSearchParams(window.location.search).getAll("category");
    boxes.forEach(function (b) {
      if (wanted.indexOf(b.value) !== -1) b.checked = true;
      b.addEventListener("change", update);
    });
    update();

    var area = document.getElementById("description");
    var wc = document.getElementById("wordcount");
    if (area && wc) {
      var count = function () {
        var words = area.value.trim() ? area.value.trim().split(/\s+/).length : 0;
        wc.textContent = words + " of 250 words";
      };
      area.addEventListener("input", count);
      count();
    }

    form.addEventListener("submit", function (ev) {
      var any = false;
      boxes.forEach(function (b) { if (b.checked) any = true; });
      if (!any) {
        ev.preventDefault();
        if (picked) picked.textContent = "Choose at least one category before sending.";
        var first = boxes[0];
        if (first) first.focus();
        return;
      }
      var btn = form.querySelector('button[type="submit"]');
      if (btn) { btn.disabled = true; btn.textContent = "Sending…"; }
    });
  }

  // ---------- Motion ----------

  // Count-up for the numbers in the hero stat strip
  function countUp(el) {
    var target = parseInt(el.textContent, 10);
    if (isNaN(target) || reduce) return;
    var start = null, dur = 1100;
    el.textContent = "0";
    function step(ts) {
      if (start === null) start = ts;
      var p = Math.min((ts - start) / dur, 1);
      var eased = 1 - Math.pow(1 - p, 3);
      el.textContent = String(Math.round(target * eased));
      if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  // Reveal targets: section heads and card grids, not individual cards (cards keep their own hover motion)
  var revealSel = [
    ".section .section__head", ".section .rows", ".section .steps", ".section .criteria",
    ".section .jurors", ".section .cols", ".section .split", ".section .faq-top",
    ".section .table-wrap", ".split-board > *", ".section .btn-row", ".section .note-line"
  ].join(",");
  var revealEls = Array.prototype.slice.call(document.querySelectorAll(revealSel));
  revealEls.forEach(function (el) {
    el.classList.add("reveal");
    // Small stagger inside each group so cards do not all appear at once
    var kids = el.children;
    if (kids.length > 1 && kids.length < 40) {
      for (var i = 0; i < kids.length; i++) kids[i].style.setProperty("--d", Math.min(i * 60, 480) + "ms");
    }
  });

  // Hero: play its entrance on load
  var heroParts = document.querySelectorAll(".hero--landing .wrap > *");
  heroParts.forEach(function (el, i) {
    el.classList.add("reveal");
    el.style.setProperty("--d", (120 + i * 130) + "ms");
  });
  var statNums = document.querySelectorAll(".stat__num");

  function showAll() {
    heroParts.forEach(function (el) { el.classList.add("is-in"); });
    revealEls.forEach(function (el) { el.classList.add("is-in"); });
    statNums.forEach(function (el) { el.classList.add("is-in"); });
  }

  if (reduce || !("IntersectionObserver" in window)) {
    showAll();
  } else {
    requestAnimationFrame(function () {
      heroParts.forEach(function (el) { el.classList.add("is-in"); });
    });
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var el = entry.target;
        el.classList.add("is-in");
        if (el.classList.contains("stat__num")) countUp(el);
        io.unobserve(el);
      });
    }, { threshold: 0.15, rootMargin: "0px 0px -6% 0px" });
    revealEls.forEach(function (el) { io.observe(el); });
    statNums.forEach(function (el) { io.observe(el); });
  }

  // Hero backdrop: random approach lights, different on every visit (decorative, so it is skipped without JS)
  var runway = document.querySelector(".hero__runway");
  if (runway) {
    var rnd = function (a, b) { return a + Math.random() * (b - a); };
    var frag = document.createDocumentFragment();
    for (var n = 0; n < 70; n++) {
      var light = document.createElement("span");
      var dur = rnd(9, 15);
      light.className = "runway__light";
      light.style.setProperty("--x", rnd(0, 100).toFixed(1) + "%");
      light.style.setProperty("--y", rnd(0, 100).toFixed(1) + "%");
      light.style.setProperty("--s", rnd(3, 6).toFixed(1) + "px");
      light.style.setProperty("--t", dur.toFixed(2) + "s");
      light.style.setProperty("--d", (-rnd(0, dur)).toFixed(2) + "s");
      light.style.setProperty("--o", rnd(0.6, 1).toFixed(2));
      light.style.setProperty("--dx", rnd(-40, 40).toFixed(0) + "px");
      frag.appendChild(light);
    }
    runway.appendChild(frag);
  }

  // Pointer spotlight on cards
  if (!reduce && window.matchMedia("(hover: hover)").matches) {
    var cardSel = ".row, .step, .juror, .split__col, .criteria > div, .stats li";
    document.addEventListener("pointermove", function (ev) {
      var card = ev.target.closest && ev.target.closest(cardSel);
      if (!card) return;
      var r = card.getBoundingClientRect();
      card.style.setProperty("--mx", (ev.clientX - r.left) + "px");
      card.style.setProperty("--my", (ev.clientY - r.top) + "px");
    });
  }

  // Page transition: a gold curtain covers the page, then the next page is opened
  if (!reduce) {
    var curtain = document.createElement("div");
    curtain.className = "curtain is-cover";
    curtain.setAttribute("aria-hidden", "true");
    document.body.appendChild(curtain);
    requestAnimationFrame(function () {
      setTimeout(function () { curtain.classList.remove("is-cover"); }, 60);
    });

    document.addEventListener("click", function (ev) {
      if (ev.defaultPrevented || ev.button !== 0 || ev.metaKey || ev.ctrlKey || ev.shiftKey || ev.altKey) return;
      var a = ev.target.closest && ev.target.closest("a[href]");
      if (!a || a.target || a.hasAttribute("download")) return;
      var url;
      try { url = new URL(a.href, window.location.href); } catch (err) { return; }
      if (url.origin !== window.location.origin) return;
      if (url.pathname === window.location.pathname && url.search === window.location.search) return; // same page / hash
      ev.preventDefault();
      curtain.classList.add("is-cover");
      setTimeout(function () { window.location.href = url.href; }, 560);
    });

    // Back/forward cache: never leave the curtain stuck on screen
    window.addEventListener("pageshow", function (ev) {
      if (ev.persisted) curtain.classList.remove("is-cover");
    });
  }
})();
