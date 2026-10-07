/**
 * Strona „Ulubione”: pobiera ulubione mieszkania z REST API i buduje tabelę porównania
 * (parametry w wierszach, mieszkania w kolumnach; na telefonie przewijana w poziomie).
 */
import { pobierz, usun } from './ulubione.js';

export function porownanie() {
  const kontener = document.querySelector('[data-porownanie]');
  if (!kontener) return;

  const e = JSON.parse(kontener.dataset.etykiety || '{}');
  const pusto = kontener.querySelector('[data-pusto]');
  const jezyk = kontener.dataset.jezyk === 'en' ? 'en-GB' : 'pl-PL';
  const liczba = (n) => new Intl.NumberFormat(jezyk, { maximumFractionDigits: 0 }).format(n);

  const el = (znacznik, tekst, klasy) => {
    const x = document.createElement(znacznik);
    if (tekst !== undefined) x.textContent = tekst;
    if (klasy) x.className = klasy;
    return x;
  };

  const rysuj = async () => {
    const ids = pobierz();
    kontener.querySelector('[data-tabela]')?.remove();
    pusto.hidden = ids.length > 0;
    if (!ids.length) return;

    const p = new URLSearchParams({ lang: kontener.dataset.jezyk || 'pl' });
    ids.forEach((id) => p.append('ids[]', String(id)));
    const odpowiedz = await fetch(`${kontener.dataset.rest}?${p}`, { headers: { Accept: 'application/json' } });
    if (!odpowiedz.ok) return;
    const { mieszkania } = await odpowiedz.json();
    // kolejność jak na liście ulubionych (ostatnio dodane pierwsze)
    mieszkania.sort((a, b) => ids.indexOf(a.id_ulubione) - ids.indexOf(b.id_ulubione));
    if (!mieszkania.length) {
      pusto.hidden = false;
      return;
    }

    const wiersze = [
      ['inwestycja', (m) => m.inwestycja_nazwa],
      ['pietro', (m) => m.pietro_tekst],
      ['pokoje', (m) => String(m.pokoje)],
      ['metraz', (m) => m.metraz_tekst],
      ['balkon', (m) => m.balkon_tekst],
      ['widok', (m) => (m.widok_na_rzeke ? e.tak : e.nie)],
      ['ogrodek', (m) => (m.ogrodek ? e.tak : e.nie)],
      ['cena', (m) => m.cena_tekst || '-'],
      ['cena_m2', (m) => (m.cena > 0 ? `${liczba(m.cena / m.metraz)} zł` : '-')],
      ['status', (m) => m.status_etykieta],
    ];

    const owijka = el('div', undefined, 'overflow-x-auto rounded-xl bg-white');
    owijka.dataset.tabela = '';
    owijka.tabIndex = 0;
    owijka.setAttribute('role', 'region');
    owijka.setAttribute('aria-label', e.podpis);
    const tabela = el('table', undefined, 'w-full min-w-[36rem] border-collapse text-left');
    tabela.append(el('caption', e.podpis, 'sr-only'));

    const glowa = el('tr');
    glowa.append(el('td', '', 'w-40 p-4'));
    mieszkania.forEach((m) => {
      const th = el('th', undefined, 'border-b-2 border-granat p-4 align-bottom');
      th.scope = 'col';
      const a = el('a', m.numer, 'font-serif text-3xl font-normal text-granat no-underline hover:text-morze');
      a.href = m.url;
      th.append(a);
      glowa.append(th);
    });
    const thead = el('thead');
    thead.append(glowa);

    const tbody = el('tbody');
    wiersze.forEach(([klucz, wartosc]) => {
      const tr = el('tr', undefined, 'border-b border-piasek-ciemny');
      const th = el('th', e[klucz], 'p-4 text-sm font-semibold uppercase tracking-wider text-granat/75');
      th.scope = 'row';
      tr.append(th);
      mieszkania.forEach((m) => tr.append(el('td', wartosc(m), 'p-4')));
      tbody.append(tr);
    });

    const akcje = el('tr');
    akcje.append(el('td', '', 'p-4'));
    mieszkania.forEach((m) => {
      const td = el('td', undefined, 'space-y-2 p-4');
      const link = el('a', e.zobacz, 'block font-semibold');
      link.href = m.url;
      const przycisk = el('button', e.usun, 'text-sm font-semibold text-granat/75 underline underline-offset-4 hover:text-red-800');
      przycisk.type = 'button';
      przycisk.addEventListener('click', () => usun(m.id_ulubione));
      td.append(link, przycisk);
      akcje.append(td);
    });
    tbody.append(akcje);

    tabela.append(thead, tbody);
    owijka.append(tabela);
    kontener.append(owijka);
  };

  document.addEventListener('przystan:ulubione', rysuj);
  rysuj();
}
