/**
 * Wyszukiwarka mieszkań bez przeładowania strony.
 * Formularz filtrów działa bez JS (zwykły GET); tu przechwytujemy go, pytamy REST API
 * (GET /wp-json/przystan/v1/mieszkania), przygaszamy niepasujące okna elewacji, odświeżamy tabelę
 * i adres URL (wynik można wysłać linkiem). Licznik wyników jest w aria-live.
 */
export function wyszukiwarka() {
  const form = document.querySelector('[data-wyszukiwarka]');
  if (!form) return;

  const elewacja = document.querySelector('#elewacja');
  const wiersze = document.querySelector('[data-wiersze]');
  const tabela = wiersze?.closest('table');
  const brak = document.querySelector('[data-brak]');
  const licznik = document.querySelector('[data-licznik]');
  const naglowki = [...(tabela?.querySelectorAll('thead th') ?? [])].map((th) => th.textContent.trim());
  const etykietaZobacz = tabela?.dataset.zobacz ?? 'Zobacz';
  const szablonLicznika = licznik?.textContent.trim().replace(/\d+/, '{n}') ?? '{n}';

  // Na telefonie filtry są zwinięte, żeby elewacja była widoczna od razu (bez JS zostają rozwinięte).
  const filtry = form.querySelector('[data-filtry]');
  if (filtry && window.matchMedia('(max-width: 1023px)').matches && !new URLSearchParams(window.location.search).toString()) {
    filtry.open = false;
  }

  let kontroler;
  let opoznienie;

  const parametry = () => {
    const dane = new FormData(form);
    const p = new URLSearchParams();
    const pokoje = dane.getAll('pokoje[]');
    if (pokoje.length) p.set('pokoje', pokoje.join(','));
    for (const klucz of ['pietro', 'metraz_min', 'metraz_max']) {
      const wartosc = String(dane.get(klucz) ?? '').trim();
      if (wartosc !== '') p.set(klucz, wartosc);
    }
    const status = dane.get('status');
    if (status && status !== 'wszystkie') p.set('status', status);
    if (dane.get('widok')) p.set('widok', '1');
    if (dane.get('balkon')) p.set('balkon', '1');
    return p;
  };

  const szukaj = async () => {
    const p = parametry();
    const zapytanie = new URLSearchParams(p);
    zapytanie.set('lang', form.dataset.jezyk || 'pl');
    // REST oczekuje tablicy pokoi, nie tekstu z przecinkami.
    if (p.has('pokoje')) {
      zapytanie.delete('pokoje');
      p.get('pokoje').split(',').forEach((x) => zapytanie.append('pokoje[]', x));
    }

    kontroler?.abort();
    kontroler = new AbortController();
    form.setAttribute('aria-busy', 'true');

    try {
      const odpowiedz = await fetch(`${form.dataset.rest}?${zapytanie}`, { signal: kontroler.signal, headers: { Accept: 'application/json' } });
      if (!odpowiedz.ok) throw new Error(String(odpowiedz.status));
      const dane = await odpowiedz.json();
      pokazWynik(dane.mieszkania, p);
    } catch (blad) {
      if (blad.name !== 'AbortError') form.submit(); // awaryjnie zwykłe przeładowanie z filtrami
    } finally {
      form.removeAttribute('aria-busy');
    }
  };

  const pokazWynik = (mieszkania, p) => {
    const ids = new Set(mieszkania.map((m) => String(m.id)));
    const aktywne = [...p.keys()].length > 0;

    elewacja?.querySelectorAll('.lokal').forEach((lokal) => {
      lokal.classList.toggle('przygaszony', aktywne && !ids.has(lokal.dataset.id));
    });

    if (wiersze) wiersze.replaceChildren(...mieszkania.map(wiersz));
    brak?.classList.toggle('hidden', mieszkania.length > 0);
    if (licznik) licznik.textContent = szablonLicznika.replace('{n}', String(mieszkania.length));

    const url = new URL(window.location.href);
    url.search = p.toString();
    window.history.replaceState(null, '', url);
  };

  const komorka = (zawartosc, etykieta, klasy = '') => {
    const td = document.createElement('td');
    if (etykieta) td.dataset.etykieta = etykieta;
    if (klasy) td.className = klasy;
    if (zawartosc instanceof Node) td.append(zawartosc);
    else td.textContent = zawartosc;
    return td;
  };

  const wiersz = (m) => {
    const tr = document.createElement('tr');
    tr.dataset.id = m.id;

    const status = document.createElement('span');
    status.className = `status status--${m.status}`;
    status.textContent = m.status_etykieta;

    const link = document.createElement('a');
    link.href = m.url;
    link.className = 'font-semibold';
    link.textContent = etykietaZobacz;
    const ukryty = document.createElement('span');
    ukryty.className = 'sr-only';
    ukryty.textContent = ` ${m.numer}`;
    link.append(ukryty);

    tr.append(
      komorka(m.numer, naglowki[0], 'td-numer font-serif text-xl'),
      komorka(m.pietro_tekst, naglowki[1]),
      komorka(String(m.pokoje), naglowki[2]),
      komorka(m.metraz_tekst, naglowki[3]),
      komorka(m.balkon_tekst, naglowki[4]),
      komorka(m.cena_tekst || '-', naglowki[5]),
      komorka(status, naglowki[6]),
      komorka(link, '', 'td-link'),
    );
    return tr;
  };

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    szukaj();
  });

  // Zmiana filtra od razu odświeża wynik (pola liczbowe z krótkim opóźnieniem podczas pisania).
  form.addEventListener('change', () => szukaj());
  form.addEventListener('input', (e) => {
    if (e.target.type !== 'number') return;
    clearTimeout(opoznienie);
    opoznienie = setTimeout(szukaj, 450);
  });

  form.querySelector('[data-wyczysc]')?.addEventListener('click', (e) => {
    e.preventDefault();
    form.reset();
    form.querySelectorAll('input[type=checkbox]').forEach((c) => { c.checked = false; });
    form.querySelectorAll('input[type=number]').forEach((i) => { i.value = ''; });
    form.querySelectorAll('select').forEach((s) => { s.selectedIndex = 0; });
    szukaj();
  });
}
