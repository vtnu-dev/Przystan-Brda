/**
 * Dymek nad oknem elewacji przy najechaniu myszą i przy fokusie z klawiatury.
 * Pełny opis mieszkania jest w aria-label linku, więc dymek jest tylko wizualnym dodatkiem.
 */
export function elewacje() {
  document.querySelectorAll('[data-elewacja]').forEach((elewacja) => {
    const dymek = elewacja.querySelector('[data-dymek]');
    if (!dymek) return;

    const pokaz = (lokal) => {
      const { numer, szczegoly, cena, status } = lokal.dataset;
      dymek.replaceChildren(
        tekst('strong', numer, 'block font-serif text-xl font-normal'),
        tekst('span', szczegoly, 'block'),
        tekst('span', [cena, status].filter(Boolean).join(' · '), 'block text-piasek'),
      );

      const prostokat = lokal.querySelector('rect.okno').getBoundingClientRect();
      const kontener = elewacja.getBoundingClientRect();
      dymek.style.left = `${prostokat.left - kontener.left + prostokat.width / 2}px`;
      dymek.style.top = `${prostokat.top - kontener.top - 10}px`;
      dymek.hidden = false;
    };

    const ukryj = () => {
      dymek.hidden = true;
    };

    elewacja.querySelectorAll('.lokal').forEach((lokal) => {
      lokal.addEventListener('mouseenter', () => pokaz(lokal));
      lokal.addEventListener('focus', () => pokaz(lokal));
      lokal.addEventListener('mouseleave', ukryj);
      lokal.addEventListener('blur', ukryj);
    });

    // Dymek jest dodatkiem wizualnym; nie powtarzamy treści czytnikom ekranu.
    dymek.removeAttribute('role');
    dymek.setAttribute('aria-hidden', 'true');
  });
}

function tekst(znacznik, zawartosc, klasy) {
  const el = document.createElement(znacznik);
  el.className = klasy;
  el.textContent = zawartosc ?? '';
  return el;
}
