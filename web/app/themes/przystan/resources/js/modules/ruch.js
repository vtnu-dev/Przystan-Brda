/**
 * Ruch strony bez bibliotek:
 *  - odsłanianie przy przewijaniu: sekcje, nagłówki (maska), zdjęcia (od dołu jak spod wody), listy po kolei,
 *    linie wody (rysowanie), elewacja zapalająca okna piętrami, liczby w statystykach (odliczanie),
 *  - nagłówek strony z cieniem po przewinięciu,
 *  - płynne przewijanie kółkiem (Lenis, tylko mysz; klawiatura, dotyk i pasek przewijania działają zwyczajnie),
 *  - paralaksa: tło hero (zdjęcie + odbicie + rysunek razem) i [data-paralaksa],
 *  - przechył kart i światło za kursorem ([data-karta], [data-swiatlo]),
 *  - nazwy dla przejść między stronami (View Transitions) ustawiane na klikniętym elemencie.
 * Przy „ogranicz ruch” nic nie czeka na animację (CSS pokazuje stan końcowy).
 */
import Lenis from 'lenis';

const ograniczRuch = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function ponizejEkranu(el) {
  return el.getBoundingClientRect().top > window.innerHeight * 0.92;
}

/** Elementy, które same dostają klasę odsłaniania (bez dopisywania atrybutów w każdym szablonie). */
function oznaczAutomatycznie() {
  const main = document.querySelector('main');
  if (!main) return;

  main.querySelectorAll('h2.h2, section > .kontener h2, article h2').forEach((h) => {
    if (!h.closest('[data-hero], [data-karta], form, .sr-only') && !h.classList.contains('sr-only')) h.classList.add('odslon-tytul');
  });

  main.querySelectorAll('img').forEach((img) => {
    if (img.closest('[data-hero], .elewacja') || img.matches('.absolute')) return;
    // obraz z paralaksą ma własny transform, więc odsłania się jego ramka
    if (img.hasAttribute('data-paralaksa')) {
      img.parentElement.classList.add('odslon-obraz');
      return;
    }
    // zdjęcie w karcie odsłania się w swojej ramce, reszta sama
    img.classList.add('odslon-obraz');
  });

  main.querySelectorAll('ol[data-odslon], ul.odslon-lista, .tresc ul').forEach((lista) => {
    lista.removeAttribute('data-odslon');
    lista.classList.add('odslon-lista');
    [...lista.children].forEach((li, i) => li.style.setProperty('--i', String(Math.min(i, 8))));
  });
}

/** Odliczanie liczb w statystykach („36”, „120 m”, „12 min”); tekst po liczbie zostaje. */
function odliczaj(dd) {
  const dopasowanie = dd.textContent.trim().match(/^(\d+)(.*)$/s);
  if (!dopasowanie) return;
  const [, liczba, reszta] = dopasowanie;
  const cel = Number(liczba);
  if (cel < 2) return;
  const start = performance.now();
  const czas = 1200;
  const krok = (t) => {
    const p = Math.min(1, (t - start) / czas);
    const lagodnie = 1 - (1 - p) ** 3;
    dd.textContent = `${Math.round(cel * lagodnie)}${reszta}`;
    if (p < 1) requestAnimationFrame(krok);
  };
  dd.textContent = `0${reszta}`;
  requestAnimationFrame(krok);
}

export function ruch() {
  naglowek();
  if (ograniczRuch() || !('IntersectionObserver' in window)) return;

  oznaczAutomatycznie();
  przechyl();
  swiatlo();
  paralaksa();
  plynnePrzewijanie();

  // Czekają tylko elementy poniżej pierwszego ekranu: to, co widać od razu, nie mruga.
  const selektory = '[data-odslon], [data-elewacja], .odslon-tytul, .odslon-obraz, .odslon-lista, svg.linie-wody:not(.linie-wody--ruch), dl dd';
  const czekajace = [...document.querySelectorAll(selektory)].filter((el) => !el.closest('[data-hero]') && ponizejEkranu(el));
  czekajace.forEach((el) => {
    if (!el.matches('dd')) el.classList.add('czeka');
  });

  const odslon = (el) => {
    if (el.matches('[data-elewacja]')) {
      el.classList.replace('czeka', 'zapalona');
    } else if (el.matches('dd')) {
      odliczaj(el);
    } else {
      el.classList.remove('czeka');
    }
  };

  // Element zakryty maską (clip-path) ma zerowe pole, więc IntersectionObserver nigdy nie uznałby go za widoczny:
  // obserwujemy wtedy rodzica (bez maski) i odsłaniamy jego dzieci.
  const cele = new Map();
  czekajace.forEach((el) => {
    const obserwowany = el.matches('.odslon-tytul, .odslon-obraz') ? el.parentElement : el;
    if (!cele.has(obserwowany)) cele.set(obserwowany, []);
    cele.get(obserwowany).push(el);
  });

  const obserwator = new IntersectionObserver((wpisy) => {
    wpisy.forEach((w) => {
      if (!w.isIntersecting) return;
      (cele.get(w.target) ?? []).forEach(odslon);
      obserwator.unobserve(w.target);
    });
  }, { rootMargin: '0px 0px -10% 0px', threshold: 0.05 });

  cele.forEach((_, el) => obserwator.observe(el));
}

/** Nagłówek z cieniem po zjechaniu w dół (wysokość się nie zmienia, więc nic nie skacze). */
function naglowek() {
  const n = document.querySelector('header[data-naglowek]');
  if (!n) return;
  let zaplanowane = false;
  const ustaw = () => {
    zaplanowane = false;
    n.classList.toggle('przewiniety', window.scrollY > 24);
  };
  window.addEventListener('scroll', () => {
    if (!zaplanowane) {
      zaplanowane = true;
      requestAnimationFrame(ustaw);
    }
  }, { passive: true });
  ustaw();
}

/** Płynne przewijanie kółkiem myszy (Lenis). Na dotyku i przy klawiaturze przeglądarka przewija sama. */
let lenis = null;
function plynnePrzewijanie() {
  if (lenis || !window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;
  lenis = new Lenis({ duration: 1.1, smoothWheel: true, anchors: true });
  const klatka = (t) => {
    lenis.raf(t);
    requestAnimationFrame(klatka);
  };
  requestAnimationFrame(klatka);
}

/**
 * Paralaksa: tło hero odjeżdża wolniej i lekko się przybliża (głębia), zdjęcia [data-paralaksa]
 * przesuwają się w ramkach. Tylko transform, liczone raz na klatkę przy przewijaniu.
 */
function paralaksa() {
  const tlo = document.querySelector('[data-hero-tlo]');
  const tekst = document.querySelector('[data-hero] .kontener');
  const warstwy = [...document.querySelectorAll('[data-paralaksa]')];
  if (!tlo && !warstwy.length) return;

  let klatka = 0;
  const przelicz = () => {
    klatka = 0;
    const y = window.scrollY;
    const h = window.innerHeight;
    if (tlo && y < h * 1.2) {
      tlo.style.transform = `translate3d(0, ${(y * 0.35).toFixed(1)}px, 0) scale(${(1 + y * 0.00012).toFixed(4)})`;
      if (tekst) tekst.style.transform = `translate3d(0, ${(y * 0.12).toFixed(1)}px, 0)`;
    }
    for (const el of warstwy) {
      const r = el.parentElement.getBoundingClientRect();
      if (r.bottom < -100 || r.top > h + 100) continue;
      const k = parseFloat(el.dataset.paralaksa || '0.08');
      el.style.transform = `translate3d(0, ${((r.top + r.height / 2 - h / 2) * -k).toFixed(1)}px, 0)`;
    }
  };
  window.addEventListener('scroll', () => {
    if (!klatka) klatka = requestAnimationFrame(przelicz);
  }, { passive: true });
  przelicz();
}

/** Światło podążające za kursorem po kartach i ciemnych pasach. */
function swiatlo() {
  if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;
  document.querySelectorAll('[data-karta], [data-swiatlo]').forEach((el) => {
    el.addEventListener('pointermove', (e) => {
      const r = el.getBoundingClientRect();
      el.style.setProperty('--mx', `${(((e.clientX - r.left) / r.width) * 100).toFixed(1)}%`);
      el.style.setProperty('--my', `${(((e.clientY - r.top) / r.height) * 100).toFixed(1)}%`);
    });
  });
}

/** Lekki przechył kart 3D za kursorem (tylko mysz, maks. 4°). */
function przechyl() {
  if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;
  document.querySelectorAll('[data-karta]').forEach((karta) => {
    if (karta.hasAttribute('data-karta-bez-cienia')) return;
    karta.addEventListener('pointermove', (e) => {
      const r = karta.getBoundingClientRect();
      const x = (e.clientX - r.left) / r.width - 0.5;
      const y = (e.clientY - r.top) / r.height - 0.5;
      karta.classList.add('przechyla');
      karta.style.setProperty('--ry', `${(x * 7).toFixed(2)}deg`);
      karta.style.setProperty('--rx', `${(-y * 6).toFixed(2)}deg`);
    });
    karta.addEventListener('pointerleave', () => {
      karta.classList.remove('przechyla');
      karta.style.setProperty('--rx', '0deg');
      karta.style.setProperty('--ry', '0deg');
    });
  });
}

/**
 * Przejścia między stronami: przed wyjściem nadajemy wspólną nazwę klikniętemu numerowi mieszkania
 * albo tytułowi inwestycji, a na nowej stronie ma ją nagłówek - przeglądarka płynnie go przenosi.
 */
export function przejscia() {
  window.addEventListener('pageswap', (e) => {
    const cel = e.activation?.entry?.url;
    if (!e.viewTransition || !cel) return;

    const link = [...document.querySelectorAll('a[href]')].find((a) => a.href === cel && a.offsetParent !== null);
    if (!link) return;

    const wiersz = link.closest('tr');
    const karta = link.closest('article');
    const element = wiersz?.querySelector('.td-numer') ?? (karta ? karta.querySelector('h2, h3') : null);
    if (!element) return;

    element.style.viewTransitionName = wiersz ? 'tytul-mieszkania' : 'tytul-inwestycji';
    // jeśli strona wróci z bfcache, nazwa nie może zostać (duplikat nazwy psuje przejście)
    e.viewTransition.finished.finally(() => { element.style.viewTransitionName = ''; });
  });
}
