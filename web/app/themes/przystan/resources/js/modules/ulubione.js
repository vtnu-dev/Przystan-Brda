/**
 * Ulubione mieszkania w localStorage (bez logowania i bez ciasteczek).
 * Przyciski [data-ulubione="ID"] przełączają stan (aria-pressed), licznik w nagłówku pokazuje liczbę.
 */
const KLUCZ = 'przystan-ulubione';
const MAKS = 12;

export function pobierz() {
  try {
    const lista = JSON.parse(localStorage.getItem(KLUCZ) || '[]');
    return Array.isArray(lista) ? lista.filter(Number.isInteger).slice(0, MAKS) : [];
  } catch {
    return [];
  }
}

function zapisz(lista) {
  try {
    localStorage.setItem(KLUCZ, JSON.stringify(lista.slice(0, MAKS)));
  } catch {
    /* tryb prywatny: ulubione działają do końca wizyty */
  }
  document.dispatchEvent(new CustomEvent('przystan:ulubione', { detail: lista }));
}

export function przelacz(id) {
  const lista = pobierz();
  const nowa = lista.includes(id) ? lista.filter((x) => x !== id) : [id, ...lista];
  zapisz(nowa);
  return nowa.includes(id);
}

export function usun(id) {
  zapisz(pobierz().filter((x) => x !== id));
}

function odswiez() {
  const lista = pobierz();

  document.querySelectorAll('[data-ulubione]').forEach((b) => {
    const wlaczone = lista.includes(Number(b.dataset.ulubione));
    b.classList.remove('invisible');
    b.removeAttribute('tabindex');
    b.setAttribute('aria-pressed', String(wlaczone));
    b.setAttribute('aria-label', wlaczone ? b.dataset.usun : b.dataset.dodaj);
  });

  document.querySelectorAll('[data-ulubione-licznik]').forEach((licznik) => {
    licznik.textContent = lista.length ? String(lista.length) : '';
    licznik.classList.toggle('hidden', lista.length === 0);
  });
}

export function ulubione() {
  document.addEventListener('click', (e) => {
    const przycisk = e.target.closest('[data-ulubione]');
    if (!przycisk) return;
    e.preventDefault();
    przelacz(Number(przycisk.dataset.ulubione));
  });

  document.addEventListener('przystan:ulubione', odswiez);
  document.addEventListener('przystan:ulubione-odswiez', odswiez);
  window.addEventListener('storage', (e) => e.key === KLUCZ && odswiez()); // inna karta przeglądarki
  odswiez();
}
