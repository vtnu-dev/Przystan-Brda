/**
 * Elewacja: karta mieszkania obok okna (mysz, fokus z klawiatury, stuknięcie; na telefonie pod schematem),
 * bez szablonu karty dymek nad oknem, oraz synchronizacja z listą -
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

    const wzor = elewacja.querySelector('[data-karta-wzor]');
    const karta = wzor ? wzor.content.firstElementChild.cloneNode(true) : null;
    if (karta) {
      dymek.hidden = true;
      elewacja.append(karta);
    }
    const telefon = matchMedia('(max-width: 767px)');
    const bezNajechania = matchMedia('(hover: none)');
    let wybrany = null;
    let przypiety = false;
    let zegar = 0;

    const pokazKarte = (lokal) => {
      const d = lokal.dataset;
      const pole = (k) => karta.querySelector(`[data-k="${k}"]`);
      pole('numer').textContent = d.numer;
      pole('status').textContent = d.status;
      pole('status').dataset.stan = d.stan;
      pole('metraz').textContent = d.metraz;
      pole('pokoje').textContent = d.pokoje;
      pole('pietro').textContent = d.pietro;
      pole('dodatek').textContent = d.dodatek ? ` · ${d.dodatek}` : '';
      pole('cena').textContent = d.cena;
      pole('url').href = lokal.getAttribute('href');
      const rzut = pole('rzut');
      rzut.hidden = !d.rzut;
      if (d.rzut) rzut.src = d.rzut;
      elewacja.querySelectorAll('.lokal.wybrany').forEach((l) => l.classList.remove('wybrany'));
      lokal.classList.add('wybrany');
      wybrany = lokal;
      karta.classList.add('widoczna');
      if (telefon.matches) {
        karta.style.left = karta.style.top = '';
        return;
      }
      // obok okna: z prawej, a gdy brak miejsca z lewej; w pionie na wysokości okna, w granicach schematu
      const o = lokal.querySelector('rect.okno').getBoundingClientRect();
      const k = elewacja.getBoundingClientRect();
      const w = karta.offsetWidth;
      const h = karta.offsetHeight;
      let x = o.right - k.left + 14;
      if (x + w > k.width) x = o.left - k.left - w - 14;
      const y = Math.min(Math.max(o.top - k.top + o.height / 2 - h / 2, 0), Math.max(k.height - h, 0));
      karta.style.left = `${Math.max(x, 0)}px`;
      karta.style.top = `${y}px`;
    };

    const pokaz = (lokal) => {
      clearTimeout(zegar);
      if (karta) {
        pokazKarte(lokal);
      } else {
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
      }
      wiersz(lokal.dataset.id)?.classList.add('aktywny');
    };

    const ukryj = (lokal, natychmiast = false) => {
      wiersz(lokal.dataset.id)?.classList.remove('aktywny');
      if (!karta) {
        dymek.classList.remove('widoczny');
        return;
      }
      // na telefonie karta zostaje pod schematem z ostatnio wybranym mieszkaniem
      if (telefon.matches || przypiety) return;
      clearTimeout(zegar);
      zegar = setTimeout(() => {
        if (karta.matches(':hover, :focus-within')) return;
        karta.classList.remove('widoczna');
        wybrany?.classList.remove('wybrany');
      }, natychmiast ? 0 : 250);
    };

    elewacja.querySelectorAll('.lokal').forEach((lokal) => {
      lokal.addEventListener('mouseenter', () => pokaz(lokal));
      lokal.addEventListener('focus', () => pokaz(lokal));
      lokal.addEventListener('mouseleave', () => ukryj(lokal));
      lokal.addEventListener('blur', () => ukryj(lokal));
      // dotyk i telefon: pierwsze stuknięcie pokazuje kartę, przejście przyciskiem „Zobacz mieszkanie”
      lokal.addEventListener('click', (e) => {
        if (!karta || !(bezNajechania.matches || telefon.matches)) return;
        if (wybrany === lokal && karta.classList.contains('widoczna') && !telefon.matches) return;
        e.preventDefault();
        przypiety = true;
        pokaz(lokal);
      });
    });

    if (karta) {
      karta.addEventListener('mouseleave', () => wybrany && ukryj(wybrany));
      karta.addEventListener('focusout', (e) => {
        if (!karta.contains(e.relatedTarget) && wybrany) ukryj(wybrany);
      });
      elewacja.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape' || !wybrany) return;
        przypiety = false;
        const okno = wybrany;
        ukryj(okno, true);
        if (karta.contains(document.activeElement)) okno.focus();
      });
      // telefon: od razu karta pierwszego wolnego mieszkania pod schematem
      const pierwszeWolne = elewacja.querySelector('.lokal--wolne:not(.przygaszony)');
      if (telefon.matches && pierwszeWolne) pokazKarte(pierwszeWolne);
    }

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
