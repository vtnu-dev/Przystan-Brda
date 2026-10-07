/**
 * Menu na telefonie: przycisk z aria-expanded, zamykanie klawiszem Escape i po kliknięciu poza menu.
 */
export function menu() {
  const przycisk = document.querySelector('[data-menu-przycisk]');
  const lista = document.querySelector('[data-menu]');
  if (!przycisk || !lista) return;

  const ustaw = (otwarte) => {
    przycisk.setAttribute('aria-expanded', String(otwarte));
    lista.classList.toggle('hidden', !otwarte);
  };

  przycisk.addEventListener('click', () => ustaw(przycisk.getAttribute('aria-expanded') !== 'true'));

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && przycisk.getAttribute('aria-expanded') === 'true') {
      ustaw(false);
      przycisk.focus();
    }
  });

  document.addEventListener('click', (e) => {
    if (przycisk.getAttribute('aria-expanded') === 'true' && !e.target.closest('[data-naglowek]')) {
      ustaw(false);
    }
  });
}
