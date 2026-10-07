/**
 * Hero B: falujące odbicie budynku w rzece. Liczy Worker na OffscreenCanvas, więc wątek główny
 * (przewijanie, kliknięcia) zostaje wolny. Bez OffscreenCanvas albo przy „ogranicz ruch” zostaje zdjęcie.
 * Ruch zatrzymuje się sam po dwóch „rundach”, przycisk pozwala go zatrzymać wcześniej (WCAG 2.2.2).
 */
const START_PO_SZKICU = 3400; // ms: rysunek z rzeki (A) gaśnie po ok. 3,4 s

export function hero() {
  const sekcja = document.querySelector('[data-hero]');
  const canvas = sekcja?.querySelector('[data-odbicie]');
  const obraz = sekcja?.querySelector('img');
  if (!canvas || !obraz) return;
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  if (!('transferControlToOffscreen' in canvas) || !('Worker' in window)) return;

  const przycisk = sekcja.querySelector('[data-ruch-pauza]');
  let worker;
  let wstrzymane = false;

  const start = async () => {
    try {
      await obraz.decode();
    } catch {
      return;
    }
    const offscreen = canvas.transferControlToOffscreen();
    worker = new Worker(new URL('./odbicie-worker.js', import.meta.url), { type: 'module' });
    worker.onmessage = (e) => {
      // klasa z Tailwinda (utilities) wygrywa z regułami komponentów, więc podmieniamy ją wprost
      if (e.data === 'gotowe') canvas.classList.replace('opacity-0', 'opacity-100');
    };

    const rozmiar = () => {
      const r = canvas.getBoundingClientRect();
      worker.postMessage({ typ: 'rozmiar', w: r.width, h: r.height, dpr: Math.min(window.devicePixelRatio || 1, 2) });
    };
    worker.postMessage({
      typ: 'start',
      canvas: offscreen,
      url: obraz.currentSrc || obraz.src,
      woda: Number(canvas.dataset.woda) || 0.7,
      pozycja: Number(canvas.dataset.pozycja) || 0.5,
    }, [offscreen]);
    rozmiar();
    new ResizeObserver(rozmiar).observe(canvas);

    // Pauza poza ekranem i na ukrytej karcie.
    const widocznosc = (widoczny) => worker.postMessage({ typ: 'widoczny', widoczny: widoczny && !document.hidden });
    new IntersectionObserver(([w]) => widocznosc(w.isIntersecting)).observe(sekcja);
    document.addEventListener('visibilitychange', () => widocznosc(!document.hidden));

    // Kręgi na wodzie od kursora i palca (co najwyżej co 120 ms).
    let ostatni = 0;
    const krag = (e) => {
      if (wstrzymane || e.timeStamp - ostatni < 120) return;
      ostatni = e.timeStamp;
      const r = canvas.getBoundingClientRect();
      worker.postMessage({ typ: 'krag', x: e.clientX - r.left, y: e.clientY - r.top, sila: e.type === 'pointerdown' ? 1.6 : 0.7 });
    };
    sekcja.addEventListener('pointermove', krag, { passive: true });
    sekcja.addEventListener('pointerdown', krag, { passive: true });

    if (przycisk) {
      przycisk.hidden = false;
      const etykieta = przycisk.querySelector('span');
      przycisk.addEventListener('click', () => {
        wstrzymane = !wstrzymane;
        worker.postMessage({ typ: wstrzymane ? 'pauza' : 'wznow' });
        przycisk.setAttribute('aria-pressed', String(wstrzymane));
        etykieta.textContent = wstrzymane ? przycisk.dataset.wznow : przycisk.dataset.pauza;
        przycisk.querySelector('[data-ikona-pauza]').toggleAttribute('hidden', wstrzymane);
        przycisk.querySelector('[data-ikona-wznow]').toggleAttribute('hidden', !wstrzymane);
      });
    }
  };

  // Po pierwszej klatce i po rysunku A; ciężka praca nie konkuruje z LCP.
  const kiedyBezczynny = window.requestIdleCallback ?? ((f) => setTimeout(f, 200));
  setTimeout(() => kiedyBezczynny(start, { timeout: 1500 }), START_PO_SZKICU);
}
