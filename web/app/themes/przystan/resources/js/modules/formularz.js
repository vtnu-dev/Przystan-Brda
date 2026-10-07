/**
 * Formularz zapytania przez REST API (POST /wp-json/przystan/v1/zapytania).
 * Błędy przy polach (aria-invalid + opis), komunikat w regionie aria-live, fokus na komunikat.
 * Bez JS formularz idzie zwykłą drogą przez admin-post.php.
 */
export function formularze() {
  document.querySelectorAll('[data-formularz]').forEach((form) => {
    const komunikat = form.querySelector('[data-komunikat]');
    const przycisk = form.querySelector('[data-wyslij]');

    const pokazKomunikat = (tekst, ok) => {
      const p = document.createElement('p');
      p.className = `rounded-lg px-4 py-3 font-semibold ${ok ? 'bg-morze text-white' : 'bg-white text-red-800'}`;
      p.tabIndex = -1;
      p.textContent = tekst;
      komunikat.replaceChildren(p);
      p.focus();
    };

    const wyczyscBledy = () => {
      form.querySelectorAll('[data-blad]').forEach((el) => { el.textContent = ''; });
      form.querySelectorAll('[aria-invalid]').forEach((el) => el.removeAttribute('aria-invalid'));
    };

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      wyczyscBledy();
      przycisk.disabled = true;

      try {
        const odpowiedz = await fetch(form.dataset.rest, {
          method: 'POST',
          body: new FormData(form),
          headers: { Accept: 'application/json' },
        });
        const dane = await odpowiedz.json();

        if (dane.ok) {
          form.querySelectorAll('input:not([type=hidden]):not([type=checkbox]), textarea').forEach((el) => { el.value = ''; });
          form.querySelectorAll('input[type=checkbox]').forEach((el) => { el.checked = false; });
          pokazKomunikat(dane.komunikat, true);
          return;
        }

        Object.entries(dane.bledy ?? {}).forEach(([pole, tekst]) => {
          const opis = form.querySelector(`[data-blad="${pole}"]`);
          const wejscie = form.elements.namedItem(pole);
          if (opis) opis.textContent = tekst;
          if (wejscie instanceof Element) wejscie.setAttribute('aria-invalid', 'true');
        });
        pokazKomunikat(dane.komunikat, false);
      } catch {
        form.submit(); // awaryjnie wersja bez JS
      } finally {
        przycisk.disabled = false;
      }
    });
  });
}
