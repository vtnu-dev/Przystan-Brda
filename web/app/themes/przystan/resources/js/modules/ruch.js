/**
 * Ruch strony bez bibliotek: odsłanianie sekcji, elewacja zapalająca okna piętrami,
 * nazwy dla przejść między stronami (View Transitions) ustawiane na klikniętym elemencie.
 * Przy „ogranicz ruch” nic nie czeka na animację (CSS pokazuje stan końcowy).
 */
const ograniczRuch = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function ponizejEkranu(el) {
  return el.getBoundingClientRect().top > window.innerHeight * 0.92;
}

export function ruch() {
  if (ograniczRuch() || !('IntersectionObserver' in window)) return;

  // Czekają tylko elementy poniżej pierwszego ekranu: to, co widać od razu, nie mruga.
  const odslon = [...document.querySelectorAll('[data-odslon]')].filter(ponizejEkranu);
  const elewacje = [...document.querySelectorAll('[data-elewacja]')].filter(ponizejEkranu);
  odslon.forEach((el) => el.classList.add('czeka'));
  elewacje.forEach((el) => el.classList.add('czeka'));

  const obserwator = new IntersectionObserver((wpisy) => {
    wpisy.forEach((w) => {
      if (!w.isIntersecting) return;
      const el = w.target;
      if (el.matches('[data-elewacja]')) {
        el.classList.replace('czeka', 'zapalona');
      } else {
        el.classList.remove('czeka');
      }
      obserwator.unobserve(el);
    });
  }, { rootMargin: '0px 0px -12% 0px', threshold: 0.15 });

  [...odslon, ...elewacje].forEach((el) => obserwator.observe(el));
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
