/**
 * Elewacja: dymek nad oknem (mysz i fokus z klawiatury) oraz synchronizacja z listą -
 * najechanie na wiersz tabeli podświetla okno, a najechanie na okno podświetla wiersz.
 * Pełny opis mieszkania jest w aria-label linku, więc dymek jest tylko wizualnym dodatkiem.
 */
export function elewacje() {
  document.querySelectorAll('[data-elewacja]').forEach((elewacja) => {
    const dymek = elewacja.querySelector('[data-dymek]');
    if (!dymek) return;

    // Dymek jest dodatkiem wizualnym; nie powtarzamy treści czytnikom ekranu.
    dymek.removeAttribute('role');
    dymek.setAttribute('aria-hidden', 'true');
    dymek.hidden = false;

    const wiersz = (id) => document.querySelector(`[data-wiersze] tr[data-id="${id}"]`);

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
      dymek.classList.add('widoczny');
      wiersz(lokal.dataset.id)?.classList.add('aktywny');
    };

    const ukryj = (lokal) => {
      dymek.classList.remove('widoczny');
      wiersz(lokal.dataset.id)?.classList.remove('aktywny');
    };

    elewacja.querySelectorAll('.lokal').forEach((lokal) => {
      lokal.addEventListener('mouseenter', () => pokaz(lokal));
      lokal.addEventListener('focus', () => pokaz(lokal));
      lokal.addEventListener('mouseleave', () => ukryj(lokal));
      lokal.addEventListener('blur', () => ukryj(lokal));
    });

    // Wiersz tabeli → okno (wiersze buduje też JS wyszukiwarki, więc nasłuch na tbody).
    const tbody = document.querySelector('[data-wiersze]');
    if (!tbody || elewacja.id !== 'elewacja') return;
    const okno = (tr) => tr && elewacja.querySelector(`.lokal[data-id="${tr.dataset.id}"]`);
    tbody.addEventListener('mouseover', (e) => {
      const tr = e.target.closest('tr[data-id]');
      elewacja.querySelectorAll('.lokal.aktywny').forEach((l) => l.classList.remove('aktywny'));
      okno(tr)?.classList.add('aktywny');
    });
    tbody.addEventListener('mouseleave', () => {
      elewacja.querySelectorAll('.lokal.aktywny').forEach((l) => l.classList.remove('aktywny'));
    });
  });
}

function tekst(znacznik, zawartosc, klasy) {
  const el = document.createElement(znacznik);
  el.className = klasy;
  el.textContent = zawartosc ?? '';
  return el;
}
