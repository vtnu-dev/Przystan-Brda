/**
 * Kalkulator raty kredytu (raty równe) na karcie mieszkania.
 * rata = K · r / (1 − (1 + r)^−n), gdzie r = oprocentowanie roczne / 12, n = liczba miesięcy.
 */
export function rata(kwota, procentRoczny, lata) {
  const n = Math.round(lata * 12);
  const r = procentRoczny / 100 / 12;
  if (n <= 0) return 0;
  if (r === 0) return kwota / n;
  return (kwota * r) / (1 - (1 + r) ** -n);
}

export function kalkulatory() {
  document.querySelectorAll('[data-kalkulator]').forEach((k) => {
    const cena = Number(k.dataset.cena) || 0;
    const jezyk = k.dataset.jezyk === 'en' ? 'en-GB' : 'pl-PL';
    const zl = (liczba) => `${new Intl.NumberFormat(jezyk, { maximumFractionDigits: 0 }).format(liczba)} zł`;
    const pole = (nazwa) => k.querySelector(`[data-pole="${nazwa}"]`);
    const wynik = (nazwa, tekst) => {
      const el = k.querySelector(`[data-wynik="${nazwa}"]`);
      if (el) el.textContent = tekst;
    };

    const licz = () => {
      const wklad = Number(pole('wklad').value);
      const lata = Number(pole('lata').value);
      const procent = Number(pole('procent').value);
      const kwota = cena * (1 - wklad / 100);

      wynik('wklad', `${wklad}% · ${zl(cena * (wklad / 100))}`);
      wynik('lata', `${lata} ${jezyk === 'pl-PL' ? 'lat' : 'years'}`);
      wynik('procent', `${new Intl.NumberFormat(jezyk, { minimumFractionDigits: 1 }).format(procent)}%`);
      wynik('kredyt', zl(kwota));
      wynik('rata', zl(rata(kwota, procent, lata)));
    };

    k.addEventListener('input', licz);
    licz();
    k.hidden = false;
  });
}
