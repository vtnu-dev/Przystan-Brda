/**
 * Worker hero B: rysuje dolną część zdjęcia (odbicie w rzece) paskami przesuniętymi w poziomie.
 * Fala tła: sinus zależny od wiersza i czasu, silniejszy bliżej widza. Kręgi od kursora/palca:
 * fala rozchodząca się od punktu (wtedy wiersz dzielimy na kawałki, żeby krąg był okrągły).
 * Zdjęcie jest dopasowane jak CSS object-cover z object-position (pozycja w poziomie).
 */
let ctx;
let bitmapa;
let W = 0;
let H = 0;
let dpr = 1;
let woda = 0.7;
let pozycja = 0.5;
let widoczny = true;
let wstrzymane = false;
let klatka = 0;
let startCzasu = 0;
let tloDo = 0; // do kiedy (ms) faluje tło: dwie „rundy”, potem woda się uspokaja
let kregi = [];

const RUNDA = 8000;
const WYGASANIE = 2500;

function dopasowanie() {
  const skala = Math.max(W / bitmapa.width, H / bitmapa.height);
  const dw = bitmapa.width * skala;
  const dh = bitmapa.height * skala;
  return { skala, dw, dh, ox: (W - dw) * pozycja, oy: (H - dh) / 2 };
}

function silaTla(t) {
  if (t < tloDo) return 1;
  return Math.max(0, 1 - (t - tloDo) / WYGASANIE);
}

function rysuj(t) {
  const { skala, dw, oy, dh, ox } = dopasowanie();
  const gora = Math.max(0, Math.floor(oy + woda * dh));
  const krok = Math.max(2, Math.round(2 * dpr));
  const tlo = silaTla(t);
  const czas = (t - startCzasu) / 1000;
  kregi = kregi.filter((k) => t - k.t0 < 3200);

  ctx.clearRect(0, 0, W, H);
  if (gora >= H) return;

  for (let y = gora; y < H; y += krok) {
    const glebokosc = (y - gora) / (H - gora); // 0 przy brzegu, 1 przy dolnej krawędzi
    const amp = (1.5 + 7 * glebokosc) * dpr * tlo;
    const fala = amp * Math.sin(y * 0.045 / dpr + czas * 1.7) + amp * 0.45 * Math.sin(y * 0.11 / dpr - czas * 2.6);
    const sy = (y - oy) / skala;
    const sh = krok / skala;

    if (!kregi.length) {
      ctx.drawImage(bitmapa, 0, sy, bitmapa.width, sh, ox + fala, y, dw, krok);
      continue;
    }

    // Z kręgami: wiersz w kawałkach, każdy przesunięty według odległości od środka kręgu.
    const kawalki = 16;
    const kw = W / kawalki;
    for (let i = 0; i < kawalki; i++) {
      const x = i * kw + kw / 2;
      let dx = fala;
      for (const k of kregi) {
        const wiek = (t - k.t0) / 1000;
        const d = Math.hypot(x - k.x, (y - k.y) * 1.8); // spłaszczenie: krąg na wodzie widziany z boku
        const front = wiek * 260 * dpr;
        const pas = Math.exp(-(((d - front) / (60 * dpr)) ** 2));
        dx += k.sila * 9 * dpr * pas * Math.sin(d * 0.06 / dpr - wiek * 9) * Math.exp(-wiek * 1.1);
      }
      const sx = (i * kw - ox) / skala;
      ctx.drawImage(bitmapa, sx, sy, kw / skala, sh, i * kw + dx, y, kw + 1, krok);
    }
  }
}

function petla(t) {
  klatka = 0;
  if (!bitmapa || !W || wstrzymane || !widoczny) return;
  rysuj(t);
  if (silaTla(t) > 0 || kregi.length) {
    klatka = requestAnimationFrame(petla);
  }
}

function obudz() {
  if (!klatka && bitmapa && W && widoczny && !wstrzymane) {
    klatka = requestAnimationFrame(petla);
  }
}

self.onmessage = async ({ data }) => {
  switch (data.typ) {
    case 'start': {
      ctx = data.canvas.getContext('2d', { alpha: true });
      woda = data.woda;
      pozycja = data.pozycja;
      const odpowiedz = await fetch(data.url);
      bitmapa = await createImageBitmap(await odpowiedz.blob());
      startCzasu = performance.now();
      tloDo = startCzasu + 2 * RUNDA;
      if (W) {
        rysuj(startCzasu);
        self.postMessage('gotowe');
      }
      obudz();
      break;
    }
    case 'rozmiar': {
      dpr = data.dpr;
      W = Math.round(data.w * dpr);
      H = Math.round(data.h * dpr);
      if (ctx) {
        ctx.canvas.width = W;
        ctx.canvas.height = H;
        if (bitmapa) {
          rysuj(performance.now());
          self.postMessage('gotowe');
        }
      }
      obudz();
      break;
    }
    case 'krag':
      kregi.push({ x: data.x * dpr, y: data.y * dpr, sila: data.sila, t0: performance.now() });
      if (kregi.length > 6) kregi.shift();
      obudz();
      break;
    case 'widoczny':
      widoczny = data.widoczny;
      obudz();
      break;
    case 'pauza':
      wstrzymane = true;
      break;
    case 'wznow':
      wstrzymane = false;
      tloDo = performance.now() + RUNDA;
      obudz();
      break;
  }
};
