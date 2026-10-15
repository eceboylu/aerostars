(function () {
  "use strict";

  var reduce = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  var toggle = document.querySelector(".nav-toggle");
  var nav = document.getElementById("nav");
  if (toggle && nav) {
    var setMenu = function (open) {
      toggle.setAttribute("aria-expanded", String(open));
      toggle.textContent = open ? "Close" : "Menu";
      nav.classList.toggle("is-open", open);
    };
    toggle.addEventListener("click", function () {
      setMenu(toggle.getAttribute("aria-expanded") !== "true");
    });
    document.addEventListener("keydown", function (ev) {
      if (ev.key === "Escape" && nav.classList.contains("is-open")) { setMenu(false); toggle.focus(); }
    });
    document.addEventListener("pointerdown", function (ev) {
      if (nav.classList.contains("is-open") && !nav.contains(ev.target) && !toggle.contains(ev.target)) setMenu(false);
    });
    nav.addEventListener("click", function (ev) {
      if (ev.target.closest && ev.target.closest("a") && nav.classList.contains("is-open")) setMenu(false);
    });
    window.addEventListener("resize", function () {
      if (window.innerWidth > 860 && nav.classList.contains("is-open")) setMenu(false);
    });
  }

  var markBrokenImages = function (imageSelector, parentSelector, brokenClass) {
    document.querySelectorAll(imageSelector).forEach(function (img) {
      var markParent = function () {
        var parent = img.closest(parentSelector);
        if (parent) parent.classList.add(brokenClass);
      };
      if (img.complete && img.naturalWidth === 0) markParent();
      img.addEventListener("error", markParent);
    });
  };
  markBrokenImages(".brand img", ".brand", "is-broken");
  markBrokenImages(".juror__photo", ".juror", "image-unavailable");

  var chips = document.querySelectorAll("[data-filter]");
  var blocks = document.querySelectorAll("[data-family]");
  chips.forEach(function (chip) {
    chip.addEventListener("click", function () {
      var want = chip.getAttribute("data-filter");
      chips.forEach(function (c) { c.setAttribute("aria-pressed", String(c === chip)); });
      blocks.forEach(function (b) {
        b.hidden = !(want === "all" || b.getAttribute("data-family") === want);
      });
    });
  });

  var form = document.getElementById("entry-form");
  if (form) {
    var submitButton = form.querySelector('button[type="submit"]');
    var tokenMeta = document.querySelector('meta[name="csrf-token"]');
    if (tokenMeta && !form.querySelector('input[name="_token"]')) {
      var token = document.createElement("input");
      token.type = "hidden";
      token.name = "_token";
      token.value = tokenMeta.getAttribute("content");
      form.appendChild(token);
    }

    var old = window.flyOldInput || {};
    Object.keys(old).forEach(function (name) {
      if (name === "_token" || name === "categories") return;
      var field = form.elements[name];
      if (!field || field.type === "file") return;
      if (field.type === "checkbox") { field.checked = old[name] === "1"; return; }
      field.value = old[name];
    });

    var boxes = form.querySelectorAll('input[name="categories[]"]');
    if (Array.isArray(old.categories)) {
      boxes.forEach(function (b) { b.checked = old.categories.indexOf(b.value) !== -1; });
    }
    var picked = document.getElementById("picked");
    var countChecked = function () {
      var checked = 0;
      boxes.forEach(function (b) { if (b.checked) checked++; });
      return checked;
    };
    var update = function () {
      var n = countChecked();
      if (!picked) return;
      if (n === 0) picked.textContent = "No categories selected yet";
      else if (n === 1) picked.textContent = "1 category selected";
      else picked.textContent = n + " categories selected";
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
      if (countChecked() === 0) {
        ev.preventDefault();
        if (picked) picked.textContent = "Choose at least one category before sending.";
        if (boxes[0]) boxes[0].focus();
        return;
      }
      if (submitButton) { submitButton.disabled = true; submitButton.textContent = "Sending…"; }
    });

    window.addEventListener("pageshow", function () {
      if (submitButton && submitButton.disabled) { submitButton.disabled = false; submitButton.textContent = "Send application"; }
    });
  }

  var runway = document.querySelector(".hero__runway");
  if (runway && !runway.children.length) {
    var rnd = function (a, b) { return a + Math.random() * (b - a); };
    var frag = document.createDocumentFragment();
    for (var n = 0; n < 40; n++) {
      var light = document.createElement("span");
      var dur = rnd(9, 15);
      light.className = "runway__light";
      light.style.setProperty("--x", rnd(0, 100).toFixed(1) + "%");
      light.style.setProperty("--y", rnd(0, 100).toFixed(1) + "%");
      light.style.setProperty("--s", rnd(3, 6).toFixed(1) + "px");
      light.style.setProperty("--t", dur.toFixed(2) + "s");
      light.style.setProperty("--d", (-rnd(0, dur)).toFixed(2) + "s");
      frag.appendChild(light);
    }
    runway.appendChild(frag);
  }

  var accordions = document.querySelectorAll("details[name]");
  if (accordions.length && Element.prototype.animate) {
    var heightWhen = function (d, open) {
      var was = d.open;
      d.open = open;
      var h = d.getBoundingClientRect().height;
      d.open = was;
      return h;
    };

    var setOpen = function (d, open) {
      var from = d.getBoundingClientRect().height;
      if (d._anim) { d._anim.cancel(); d._anim = null; }
      d._target = open;
      d.classList.toggle("is-closing", !open);
      if (reduce) { d.open = open; d.classList.remove("is-closing"); return; }
      var to = heightWhen(d, open);
      d.open = true;
      d.style.overflow = "hidden";
      var anim = d.animate({ height: [from + "px", to + "px"] }, { duration: 320, easing: "cubic-bezier(0.4, 0, 0.2, 1)" });
      d._anim = anim;
      anim.onfinish = function () {
        d._anim = null;
        d.open = open;
        d.style.overflow = "";
        d.classList.remove("is-closing");
      };
    };

    accordions.forEach(function (d) {
      d.dataset.accordion = d.getAttribute("name");
      d.removeAttribute("name");
      d._target = d.open;
      var summary = d.querySelector("summary");
      if (!summary) return;
      summary.addEventListener("click", function (ev) {
        ev.preventDefault();
        var opening = !d._target;
        if (opening) {
          document.querySelectorAll('details[data-accordion="' + d.dataset.accordion + '"]').forEach(function (other) {
            if (other !== d && other._target) setOpen(other, false);
          });
        }
        setOpen(d, opening);
      });
    });
  }

  if ("IntersectionObserver" in window && !reduce) {
    var selectors = [".section__head", ".row", ".split__col", ".board", ".cols", ".criteria > div", ".step", ".juror", ".faq-top", ".winner", ".family-block__head", ".note"];
    var nodes = Array.prototype.slice.call(document.querySelectorAll(selectors.join(",")));
    nodes.forEach(function (node) { node.classList.add("reveal-target"); });
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) { entry.target.classList.add("is-visible"); observer.unobserve(entry.target); }
      });
    }, { threshold: 0.07, rootMargin: "0px 0px 30px 0px" });
    nodes.forEach(function (node) { observer.observe(node); });
    document.documentElement.classList.add("js-ready");
  }
})();
