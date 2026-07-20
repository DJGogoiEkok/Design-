// Design Plus Assam — redesign scripts

async function includePartials() {
  const targets = document.querySelectorAll('[data-include]');
  await Promise.all(Array.from(targets).map(async el => {
    const file = el.getAttribute('data-include');
    const res = await fetch('partials/' + file + '?v=6');
    el.innerHTML = await res.text();
    
    // Loop the hero art animation 4s after it finishes (~16.7s animation + 4s = 20.7s)
    if (file === 'hero-art.svg') {
      let loopCount = 0;
      const intervalId = setInterval(() => {
        if (loopCount >= 4) {
          clearInterval(intervalId);
          return;
        }
        loopCount++;
        
        const svgContent = el.innerHTML;
        // Fade out slightly before resetting
        el.style.transition = "opacity 0.4s ease";
        el.style.opacity = "0";
        setTimeout(() => {
          el.innerHTML = '';
          el.innerHTML = svgContent; // Reflow resets the CSS animation
          el.style.opacity = ""; // clear the inline override, let the CSS opacity (0.3, or 0.15 on mobile) apply
        }, 400);
      }, 20700);
    }
  }));
}

function initHeader() {
  const header = document.getElementById('site-header');
  const burger = header.querySelector('.nav-burger-box');
  const panel = document.querySelector('.menu-panel');

  if (burger && panel) {
    burger.addEventListener('click', () => {
      burger.classList.toggle('open');
      panel.classList.toggle('open');
    });
    // close the panel when a link inside it is clicked
    panel.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', () => {
        burger.classList.remove('open');
        panel.classList.remove('open');
      });
    });
  }

  // underline the active page's link
  const page = document.body.getAttribute('data-page');
  if (page) {
    header.querySelectorAll('a[data-nav]').forEach(a => {
      if (a.getAttribute('data-nav') === page) a.parentElement.classList.add('active');
    });
  }

  // promo bar stays hidden until the visitor scrolls
  window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 40);
  });
}

function initCube() {
  const cubes = document.querySelectorAll('[data-cube]');
  if (!cubes.length) return;

  const TILES = [
    { img: 'images/site/proj_homes.jpg',        idx: '01', title: 'Individual Homes' },
    { img: 'images/site/proj_highrise.jpg',      idx: '02', title: 'High Rise Apartments' },
    { img: 'images/site/proj_commercial.jpg',    idx: '03', title: 'Commercial' },
    { img: 'images/site/proj_institutional.jpg', idx: '04', title: 'Institutional' },
    { img: 'images/site/proj_township.jpg',      idx: '05', title: 'Township' },
    { img: 'images/site/proj_hospitality.jpg',   idx: '06', title: 'Hospitality' },
    { img: 'images/site/proj_healthcare.jpg',    idx: '07', title: 'Health Care' },
    { img: 'images/site/proj_interior.jpg',      idx: '08', title: 'Interior' },
    { img: 'images/site/proj_religious.jpg',     idx: '09', title: 'Religious' },
    { img: 'images/site/proj_others.jpg',        idx: '10', title: 'Others' }
  ];

  const fill = (el, t) => {
    el.innerHTML = '<img src="' + t.img + '" alt=""><div class="cube-label"><span>' +
      t.idx + '</span>' + t.title + '</div>';
  };

  // build one cube from a slice of tiles, using the same stepped-numbering
  // logic — the four side faces come to the front in order as it turns -90deg
  // axis 'y' turns side-to-side (front,right,back,left come to front);
  // axis 'x' turns top-to-bottom (front,top,back,bottom come to front)
  function makeCube(cube, tiles, axis) {
    const faceEl = (name) => {
      const el = document.createElement('div');
      el.className = 'cube-face face-' + name;
      cube.appendChild(el);
      return el;
    };
    const front = faceEl('front'), right = faceEl('right'),
          back  = faceEl('back'),  left  = faceEl('left'),
          top   = faceEl('top'),   bottom = faceEl('bottom');

    const isX = axis === 'x';
    if (isX) cube.classList.add('cube-x');
    const cycle    = isX ? [front, top, back, bottom] : [front, right, back, left];
    const leftover = isX ? [right, left]              : [top, bottom];
    cycle.forEach((el, i) => fill(el, tiles[i % tiles.length]));
    leftover.forEach((el, i) => fill(el, tiles[i % tiles.length]));

    const base = isX ? 'rotateY(-18deg) rotateX(' : 'rotateX(-20deg) rotateY(';
    cube.style.transform = base + '0deg)';

    let s = 0, angle = 0;
    return function step() {
      s += 1;
      angle -= 90;                                 // quarter turn to the next face
      cube.style.transform = base + angle + 'deg)';
      // the face now hidden at the back gets the number it shows two turns on
      fill(cycle[(s + 2) % 4], tiles[(s + 2) % tiles.length]);
    };
  }

  // distribute the tiles evenly across the cubes (01-05 | 06-10);
  // first cube turns side-to-side, second turns top-to-bottom
  const n = cubes.length;
  const per = Math.ceil(TILES.length / n);
  const steppers = Array.from(cubes).map((cube, ci) =>
    makeCube(cube, TILES.slice(ci * per, ci * per + per), ci === 1 ? 'x' : 'y')
  );

  const READ = 3200;                               // ~1s turn + ~2.2s to read
  const tick = () => steppers.forEach(fn => fn());
  let timer = setInterval(tick, READ);
  const stage = cubes[0].closest('.cube-stage');
  stage.addEventListener('mouseenter', () => clearInterval(timer));
  stage.addEventListener('mouseleave', () => { timer = setInterval(tick, READ); });
}

function initHangShow() {
  const stage = document.querySelector('[data-hang]');
  if (!stage) return;
  const cards = Array.from(stage.querySelectorAll('.service-card'));
  if (cards.length < 2) return;

  let i = 0;
  cards[0].classList.add('hang-active');   // first card starts down

  let timer;
  function advance() {
    cards[i].classList.remove('hang-active');       // current pulls up
    const next = (i + 1) % cards.length;
    // let it clear before the next one drops, so only one shows at a time
    setTimeout(() => { i = next; cards[i].classList.add('hang-active'); }, 600);
  }
  const start = () => { timer = setInterval(advance, 2800); };
  const stop = () => clearInterval(timer);
  start();
  // pause the sequence while hovering so a card can be read
  stage.addEventListener('mouseenter', stop);
  stage.addEventListener('mouseleave', start);
}

function initTeamDevelop() {
  const cards = document.querySelectorAll('.team-card');
  if (!cards.length) return;
  // develop each card (photo-negative -> original) as it scrolls into frame
  const io = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('in-view');
        io.unobserve(e.target);
      }
    });
  }, { threshold: 0.35 });
  cards.forEach(c => io.observe(c));
}

function initRevealOnScroll() {
  const els = document.querySelectorAll('.reveal');
  const io = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        io.unobserve(e.target);
      }
    });
  }, { threshold: 0.15 });
  els.forEach(el => io.observe(el));
}

function initHeroSlider() {
  const hero = document.querySelector('[data-slides]');
  if (!hero) return;
  const slides = hero.querySelectorAll('.hero-slide');
  if (slides.length < 2) return;
  let i = 0;
  setInterval(() => {
    slides[i].classList.remove('active');
    i = (i + 1) % slides.length;
    slides[i].classList.add('active');
  }, 5500);
}

function initCounters() {
  const boxes = document.querySelectorAll('[data-count]');
  if (!boxes.length) return;
  const animate = (el) => {
    const target = parseInt(el.getAttribute('data-count'), 10);
    const dur = 1400;
    const start = performance.now();
    function tick(now) {
      const p = Math.min((now - start) / dur, 1);
      el.textContent = Math.floor(p * target).toString();
      if (p < 1) requestAnimationFrame(tick);
      else el.textContent = target.toString();
    }
    requestAnimationFrame(tick);
  };
  const io = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) { animate(e.target); io.unobserve(e.target); }
    });
  }, { threshold: 0.4 });
  boxes.forEach(b => io.observe(b));
}

function initTestimonials() {
  const slider = document.querySelector('.testimonial-slider');
  if (!slider) return;
  const dotsWrap = slider.querySelector('.testimonial-dots');

  // randomly shuffle the testimonial order (Fisher-Yates)
  const slides = Array.from(slider.querySelectorAll('.testimonial-slide'));
  for (let k = slides.length - 1; k > 0; k--) {
    const j = Math.floor(Math.random() * (k + 1));
    [slides[k], slides[j]] = [slides[j], slides[k]];
  }
  // reflect the shuffled order in the DOM and reset the active card
  slides.forEach(el => { el.classList.remove('active'); slider.insertBefore(el, dotsWrap); });
  slides[0].classList.add('active');

  let i = 0;
  dotsWrap.innerHTML = '';
  slides.forEach((_, idx) => {
    const b = document.createElement('button');
    b.addEventListener('click', () => show(idx));
    dotsWrap.appendChild(b);
  });
  function show(idx) {
    slides.forEach(s => s.classList.remove('active', 'next', 'prev'));
    if (dotsWrap.children[i]) dotsWrap.children[i].classList.remove('active');
    
    i = idx;
    
    const nextIdx = (i + 1) % slides.length;
    const prevIdx = (i - 1 + slides.length) % slides.length;
    
    slides[i].classList.add('active');
    if (slides.length > 1) {
      slides[nextIdx].classList.add('next');
      if (slides.length > 2) slides[prevIdx].classList.add('prev');
    }
    
    dotsWrap.children[i].classList.add('active');
  }
  show(0);
  setInterval(() => show((i + 1) % slides.length), 6000);
}

function initTriCarousel() {
  const wrap = document.querySelector('[data-tri-carousel]');
  if (!wrap) return;
  const cards = Array.from(wrap.querySelectorAll('.service-card'));
  const n = cards.length;
  if (n < 3) return;

  const dots = document.createElement('div');
  dots.className = 'tri-dots';
  cards.forEach((_, idx) => {
    const b = document.createElement('button');
    b.addEventListener('click', () => { center = idx; render(); });
    dots.appendChild(b);
  });
  wrap.after(dots);

  let center = 0;
  const FAN = ['fan-left', 'fan-center', 'fan-right', 'fan-out-left', 'fan-out-right'];

  function render() {
    cards.forEach((c, i) => {
      c.classList.remove(...FAN);
      const d = ((i - center) % n + n) % n;   // circular distance ahead of center
      if (d === 0) c.classList.add('fan-center');
      else if (d === 1) c.classList.add('fan-right');
      else if (d === n - 1) c.classList.add('fan-left');
      else if (d === 2) c.classList.add('fan-out-right');
      else if (d === n - 2) c.classList.add('fan-out-left');
    });
    Array.from(dots.children).forEach((dot, idx) => dot.classList.toggle('active', idx === center));
  }

  render();
  const advance = () => { center = (center + 1) % n; render(); };
  let timer = setInterval(advance, 3000);
  // pause on hover so the highlighted card can be read
  wrap.addEventListener('mouseenter', () => clearInterval(timer));
  wrap.addEventListener('mouseleave', () => { timer = setInterval(advance, 3000); });
}

const MOSAIC_IMAGES = [
  'ap1.jpg', 'ap10.jpeg', 'ap11.jpeg', 'ap12.jpeg', 'ap13.jpeg', 'ap14.jpg',
  'ap15.jpg', 'ap16.jpg', 'ap17.jpg', 'ap18.jpg', 'ap2.jpg', 'ap3.jpg',
  'ap4.jpg', 'ap5.jpeg', 'ap6.jpeg', 'ap7.jpeg', 'ap8.jpeg', 'ap9.jpeg',
  'comm1.jpg', 'comm10.jpeg', 'comm11.png', 'comm12.jpeg', 'comm13.jpeg', 'comm14.jpg',
  'comm15.jpg', 'comm16.jpg', 'comm17.jpg', 'comm2.jpg', 'comm3.jpg', 'comm4.jpg',
  'comm5.jpg', 'comm6.png', 'comm7.jpg', 'comm8.jpg', 'comm9.jpeg', 'int1.jpeg',
  'int10.png', 'int2.jpg', 'int3.png', 'int4.png', 'int5.jpg', 'int6.jpeg',
  'int7.jpeg', 'int8.jpg', 'int9.png', 'oth1.png', 'oth2.jpg', 'oth3.jpg',
  'oth4.jpeg', 'res1.jpg', 'res10.jpg', 'res11.jpg', 'res12.jpg', 'res13.jpg',
  'res14.jpg', 'res15.jpg', 'res16.jpg', 'res17.jpg', 'res18.jpg', 'res19.jpg',
  'res2.jpg', 'res20.jpg', 'res21.jpg', 'res22.jpeg', 'res23.jpeg', 'res24.jpeg',
  'res3.jpg', 'res4.png', 'res5.jpeg', 'res6.jpg', 'res8.jpg', 'res9.jpeg',
  'featured-city-central-front.jpg', 'featured-city-central-roof.jpg',
  'featured-hotel-maverick.jpg', 'featured-rr-fashions.jpg'
];

function initMosaic() {
  const box = document.querySelector('[data-mosaic]');
  if (!box) return;
  const src = f => 'images/portfolio/' + f;

  let cells = [];
  let current = null;
  let gen = 0; // bumped on every rebuild so in-flight image loads from a stale layout can bail out

  function build() {
    gen++;
    box.innerHTML = '';
    cells = [];
    const TARGET = 110; // aim for ~110px squares
    const cols = Math.max(2, Math.round(box.clientWidth / TARGET));
    const rows = Math.max(2, Math.round(box.clientHeight / TARGET));
    box.style.gridTemplateColumns = 'repeat(' + cols + ', 1fr)';
    box.style.gridTemplateRows = 'repeat(' + rows + ', 1fr)';
    for (let i = 0; i < cols * rows; i++) {
      const cell = document.createElement('div');
      cell.className = 'cell';
      box.appendChild(cell);
      cells.push(cell);
    }
    if (current) applyImage(current, false);
  }

  // paint ONE image across all cells: each square shows its slice of the photo
  function applyImage(file, animate) {
    const myGen = gen;
    const targetCells = cells; // snapshot — a rebuild during the async load must not touch these
    const img = new Image();
    img.onload = () => {
      if (myGen !== gen) return; // a resize/rebuild happened while this image was loading — discard
      current = file;
      const bw = box.clientWidth, bh = box.clientHeight;
      // cover-fit the image to the whole tile
      const scale = Math.max(bw / img.naturalWidth, bh / img.naturalHeight);
      const dw = img.naturalWidth * scale, dh = img.naturalHeight * scale;
      const ox = (bw - dw) / 2, oy = (bh - dh) / 2;

      // knock out a couple of edge cells — they stay page-coloured (Figma style)
      const cols = getComputedStyle(box).gridTemplateColumns.split(' ').length;
      const rows = Math.ceil(targetCells.length / cols);
      const edgeCells = targetCells.map((_, i) => i).filter(i => {
        const c = i % cols, r = Math.floor(i / cols);
        return c === 0 || c === cols - 1 || r === 0 || r === rows - 1;
      });
      const knocked = new Set();
      while (knocked.size < Math.min(2, edgeCells.length)) {
        knocked.add(edgeCells[Math.floor(Math.random() * edgeCells.length)]);
      }

      targetCells.forEach((cell, idx) => {
        if (knocked.has(idx)) {
          // fade out whatever was there; the cell stays empty
          Array.from(cell.children).forEach(p => {
            p.classList.add('pane');
            p.style.opacity = '0';
            setTimeout(() => p.remove(), 1000);
          });
          return;
        }
        const pane = document.createElement('div');
        pane.className = 'pane';
        pane.style.backgroundImage = 'url("' + src(file) + '")';
        pane.style.backgroundSize = dw + 'px ' + dh + 'px';
        pane.style.backgroundPosition = (ox - cell.offsetLeft) + 'px ' + (oy - cell.offsetTop) + 'px';
        if (animate) {
          pane.style.opacity = '0';
          pane.style.transitionDelay = (Math.random() * 0.6).toFixed(2) + 's';
          cell.appendChild(pane);
          requestAnimationFrame(() => { pane.style.opacity = '1'; });
          // clear older panes once the fade completes
          setTimeout(() => {
            while (cell.children.length > 1) cell.removeChild(cell.firstChild);
          }, 1800);
        } else {
          cell.innerHTML = '';
          cell.appendChild(pane);
        }
      });

      placeChips(cols, rows);
    };
    img.src = src(file);
  }

  // small yellow squares straddling the grid edges / seams (Figma style),
  // relocated with every picture change
  function placeChips(cols, rows) {
    const gap = 6;
    const cw = cells[0].offsetWidth, ch = cells[0].offsetHeight;
    let chips = box.querySelectorAll('.chip');
    if (chips.length === 0) {
      for (let i = 0; i < 4; i++) {
        const el = document.createElement('div');
        el.className = 'chip';
        box.appendChild(el);
      }
      chips = box.querySelectorAll('.chip');
    }
    // candidate intersections, biased to the left column and bottom edge
    const spots = [
      { c: 0, r: 1 + Math.floor(Math.random() * (rows - 1)) },              // left edge
      { c: 0, r: Math.floor(Math.random() * rows) },                         // left edge
      { c: 1 + Math.floor(Math.random() * (cols - 1)), r: rows },            // bottom edge
      { c: Math.floor(Math.random() * 2), r: 0 },                            // top-left corner
      { c: cols, r: 1 + Math.floor(Math.random() * (rows - 1)) },            // right edge
      { c: 1 + Math.floor(Math.random() * (cols - 1)), r: 0 }                // top edge
    ];
    // shuffle and take 4 distinct spots
    spots.sort(() => Math.random() - 0.5);
    const used = [];
    for (const s of spots) {
      if (used.length === 4) break;
      if (used.some(u => u.c === s.c && u.r === s.r)) continue;
      used.push(s);
    }
    used.forEach((s, i) => {
      const size = Math.round((0.4 + Math.random() * 0.3) * Math.min(cw, ch));
      const x = s.c * (cw + gap) - gap / 2;
      const y = s.r * (ch + gap) - gap / 2;
      const el = chips[i];
      el.style.width = size + 'px';
      el.style.height = size + 'px';
      el.style.left = (x - size / 2) + 'px';
      el.style.top = (y - size / 2) + 'px';
    });
  }

  function swapImage() {
    let f;
    do { f = MOSAIC_IMAGES[Math.floor(Math.random() * MOSAIC_IMAGES.length)]; }
    while (f === current);
    applyImage(f, true);
  }

  build();
  applyImage(MOSAIC_IMAGES[Math.floor(Math.random() * MOSAIC_IMAGES.length)], false);
  let resizeT;
  window.addEventListener('resize', () => {
    clearTimeout(resizeT);
    resizeT = setTimeout(build, 250);
  });
  setInterval(swapImage, 4000);
}

function initTilt3D() {
  const selectors = [
    '.hero-cols > div', '.portfolio-item', '.team-card', '.client-card',
    '.award-item', '.contact-info-item', '.service-card',
    '.form-card', '.gallery-grid img'
  ];
  const els = Array.from(document.querySelectorAll(selectors.join(',')))
    // fan-carousel cards position themselves with transforms — leave them alone
    .filter(el => !el.closest('.tri-carousel'));

  const MAX = 9; // degrees

  els.forEach(el => {
    el.classList.add('tilt3d');
    el.addEventListener('mousemove', e => {
      const r = el.getBoundingClientRect();
      const px = (e.clientX - r.left) / r.width - 0.5;   // -0.5 .. 0.5
      const py = (e.clientY - r.top) / r.height - 0.5;
      el.style.transform =
        'perspective(900px) rotateX(' + (-py * MAX).toFixed(2) + 'deg)' +
        ' rotateY(' + (px * MAX).toFixed(2) + 'deg) translateY(-4px)';
      el.classList.add('tilting');
    });
    el.addEventListener('mouseleave', () => {
      el.style.transform = '';
      el.classList.remove('tilting');
    });
  });
}

function initPortfolioSticky() {
  const triggers = document.querySelectorAll('.portfolio-scroll-triggers .scroll-trigger');
  if (!triggers.length) return;

  const io = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        const id = e.target.id; // e.g. "proj-1"
        const container = e.target.closest('.portfolio-split-view');
        if (!container) return;
        
        const texts = container.querySelectorAll('.portfolio-text-side .text-item');
        const images = container.querySelectorAll('.portfolio-image-side .image-item');
        
        // Update texts
        texts.forEach(t => {
          if (t.getAttribute('data-target') === id) {
            t.classList.add('active');
            
            // Smoothly scroll the container so the active text is visible
            const textContainer = t.parentElement;
            const containerHeight = textContainer.clientHeight;
            const textTop = t.offsetTop; // offsetParent is textContainer
            const textHeight = t.clientHeight;
            textContainer.scrollTo({
              top: textTop - (containerHeight / 2) + (textHeight / 2),
              behavior: 'smooth'
            });
          } else {
            t.classList.remove('active');
          }
        });
        
        // Update images
        images.forEach(img => {
          if (img.id === id + '-img') {
            img.classList.add('active');
          } else {
            img.classList.remove('active');
          }
        });
      }
    });
  }, { threshold: 0.5 }); // trigger when block is 50% through the screen

  triggers.forEach(t => io.observe(t));
}

function initFakeForms() {
  document.querySelectorAll('form[data-fake-submit]').forEach(form => {
    form.addEventListener('submit', e => {
      e.preventDefault();
      const status = form.querySelector('.form-status');
      if (status) {
        status.style.display = 'block';
        status.textContent = 'Thank you — your message has been sent.';
      }
      form.reset();
    });
  });
}

// Prevent browser from restoring scroll position
if ('scrollRestoration' in window.history) {
  window.history.scrollRestoration = 'manual';
}

document.addEventListener('DOMContentLoaded', async () => {
  // Scroll to top immediately on page load
  window.scrollTo(0, 0);
  document.documentElement.scrollTop = 0;
  document.body.scrollTop = 0;

  await includePartials();

  // The slide-down menu ships inside the header partial, but the header has
  // overflow:hidden + backdrop-filter, which would clip/trap the panel inside
  // the nav bar. Lift it out to <body> so it can cover the whole page.
  const menuPanel = document.querySelector('#site-header .menu-panel');
  if (menuPanel) document.body.appendChild(menuPanel);

  // Scroll to top again after content loads
  window.scrollTo(0, 0);
  document.documentElement.scrollTop = 0;
  document.body.scrollTop = 0;

  initHeader();
  initLogoColor();
  initRevealOnScroll();
  initTeamDevelop();
  initHeroSlider();
  initCounters();
  initTestimonials();
  initTriCarousel();
  initCube();
  initMosaic();
  initTilt3D();
  initPortfolioSticky();
  initFakeForms();
});
function initLogoColor() {
  const logo = document.querySelector('.logo-word');
  if (!logo) return;

  // We add a transition in JS to ensure it's smooth
  logo.style.transition = 'color 0.3s ease';

  window.addEventListener('scroll', () => {
    // If we are at the very top, default to the ink color
    if (window.scrollY < 50) {
      logo.style.color = 'var(--ink)';
      return;
    }

    // Get the center of the logo
    const rect = logo.getBoundingClientRect();
    const x = rect.left + rect.width / 2;
    const y = rect.top + rect.height / 2;

    // Temporarily hide the header so elementsFromPoint can see what's underneath
    const header = document.getElementById('site-header');
    const oldDisplay = header.style.display;
    header.style.display = 'none';

    const elements = document.elementsFromPoint(x, y);
    header.style.display = oldDisplay; // restore immediately

    let isYellow = false;
    for (let el of elements) {
      if (el.tagName === 'SECTION' || el.tagName === 'FOOTER' || el.classList.contains('service-card')) {
        // check computed background color
        const style = window.getComputedStyle(el);
        const bg = style.backgroundColor;
        
        // check if it's yellow/gold (rgba(242, 200, 75) or similar)
        // or check for specific IDs
        if (el.id === 'testimonials-home' || el.classList.contains('service-card') || bg.includes('242, 200, 75') || bg.includes('rgb(242, 200, 75)')) {
          isYellow = true;
          break;
        }
        
        // If we hit a main section and it wasn't yellow, we stop looking
        if (el.tagName === 'SECTION') {
          break;
        }
      }
    }

    if (isYellow) {
      logo.style.color = 'var(--ink)';
    } else {
      logo.style.color = 'var(--gold)';
    }
  });
}

// Splash Screen Logic
document.addEventListener('DOMContentLoaded', () => {
  const splash = document.getElementById('splash-screen');
  const splashVid = document.getElementById('splash-video');
  const enterBtn = document.getElementById('splash-enter');

  if (splash) {
    if (window.skipSplashScreen) {
      splash.remove();
      return;
    }
    document.body.classList.add('splash-active');
    
    const hideSplash = () => {
      splash.style.opacity = '0';
      splash.style.visibility = 'hidden';
      document.body.classList.remove('splash-active');
      
      setTimeout(() => splash.remove(), 800);
    };

    if (enterBtn) enterBtn.addEventListener('click', hideSplash);
    if (splashVid) splashVid.addEventListener('ended', hideSplash);
  }
});

// 3D Studio Coverflow Slider
document.addEventListener('DOMContentLoaded', () => {
  const container = document.querySelector('.studio-slider');
  if (!container) return;

  const slides = Array.from(container.querySelectorAll('.studio-slide'));
  if (slides.length === 0) return;

  const prevBtn = document.querySelector('.studio-slider-controls .prev');
  const nextBtn = document.querySelector('.studio-slider-controls .next');
  let currentIndex = 0;
  let autoPlayInterval;

  function updateSlider() {
    slides.forEach((slide, index) => {
      // Remove all state classes
      slide.classList.remove('slide-active', 'slide-prev', 'slide-next', 'slide-prev-2', 'slide-next-2', 'slide-hidden');
      
      const diff = index - currentIndex;
      const total = slides.length;
      
      // Handle wrapping for infinite loop effect
      let offset = diff;
      if (diff < -Math.floor(total / 2)) offset += total;
      if (diff > Math.floor(total / 2)) offset -= total;

      if (offset === 0) {
        slide.classList.add('slide-active');
      } else if (offset === -1) {
        slide.classList.add('slide-prev');
      } else if (offset === 1) {
        slide.classList.add('slide-next');
      } else if (offset === -2) {
        slide.classList.add('slide-prev-2');
      } else if (offset === 2) {
        slide.classList.add('slide-next-2');
      } else {
        slide.classList.add('slide-hidden');
      }
    });
  }

  function nextSlide() {
    currentIndex = (currentIndex + 1) % slides.length;
    updateSlider();
  }

  function prevSlide() {
    currentIndex = (currentIndex - 1 + slides.length) % slides.length;
    updateSlider();
  }

  function startAutoPlay() {
    stopAutoPlay();
    autoPlayInterval = setInterval(nextSlide, 4000);
  }

  function stopAutoPlay() {
    if (autoPlayInterval) clearInterval(autoPlayInterval);
  }

  // Event Listeners
  if (nextBtn) nextBtn.addEventListener('click', () => { nextSlide(); startAutoPlay(); });
  if (prevBtn) prevBtn.addEventListener('click', () => { prevSlide(); startAutoPlay(); });

  slides.forEach((slide, index) => {
    slide.addEventListener('click', () => {
      currentIndex = index;
      updateSlider();
      startAutoPlay();
    });
  });

  const sliderContainer = document.querySelector('.studio-slider-container');
  if (sliderContainer) {
    sliderContainer.addEventListener('mouseenter', stopAutoPlay);
    sliderContainer.addEventListener('mouseleave', startAutoPlay);
  }

  // Init
  updateSlider();
  startAutoPlay();
});
