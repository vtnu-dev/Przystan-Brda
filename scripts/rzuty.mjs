/**
 * Generator rzutów mieszkań: 6 układów (A-F) w dwóch językach, SVG -> WebP.
 * Wynik: scripts/seed-media/rzuty/rzut-<typ>-<jezyk>.webp (używa go scripts/seed.php).
 *
 * Uruchomienie: npm run rzuty
 */
import { mkdir, writeFile } from 'node:fs/promises';
import sharp from 'sharp';

const SKALA = 70; // px na metr
const MARGINES = 90;

const NAZWY = {
  salon: { pl: 'Salon z aneksem', en: 'Living room + kitchen' },
  pokoj: { pl: 'Pokój', en: 'Bedroom' },
  sypialnia: { pl: 'Sypialnia', en: 'Main bedroom' },
  lazienka: { pl: 'Łazienka', en: 'Bathroom' },
  przedpokoj: { pl: 'Przedpokój', en: 'Hall' },
  garderoba: { pl: 'Garderoba', en: 'Wardrobe' },
  schowek: { pl: 'Schowek', en: 'Storage' },
  rzeka: { pl: 'strona rzeki', en: 'river side' },
};

// Pokoje: [rodzaj, x, y, szerokość, głębokość] w metrach. Górna krawędź = okna od strony rzeki.
const UKLADY = {
  A: { w: 6.4, d: 5.0, pokoje: [['salon', 0, 0, 6.4, 3.2], ['lazienka', 0, 3.2, 2.4, 1.8], ['przedpokoj', 2.4, 3.2, 4.0, 1.8]] },
  B: { w: 8.0, d: 5.5, pokoje: [['salon', 0, 0, 4.8, 3.6], ['sypialnia', 4.8, 0, 3.2, 3.6], ['lazienka', 0, 3.6, 2.4, 1.9], ['przedpokoj', 2.4, 3.6, 3.2, 1.9], ['garderoba', 5.6, 3.6, 2.4, 1.9]] },
  C: { w: 8.6, d: 6.0, pokoje: [['salon', 0, 0, 5.2, 3.8], ['sypialnia', 5.2, 0, 3.4, 3.8], ['lazienka', 0, 3.8, 2.6, 2.2], ['przedpokoj', 2.6, 3.8, 3.6, 2.2], ['schowek', 6.2, 3.8, 2.4, 2.2]] },
  D: { w: 9.0, d: 7.0, pokoje: [['salon', 0, 0, 5.4, 4.0], ['sypialnia', 5.4, 0, 3.6, 4.0], ['pokoj', 0, 4.0, 3.0, 3.0], ['lazienka', 3.0, 4.0, 2.2, 3.0], ['przedpokoj', 5.2, 4.0, 3.8, 3.0]] },
  E: { w: 10.0, d: 6.8, pokoje: [['salon', 0, 0, 5.6, 3.9], ['sypialnia', 5.6, 0, 4.4, 3.9], ['pokoj', 0, 3.9, 3.6, 2.9], ['lazienka', 3.6, 3.9, 2.4, 2.9], ['przedpokoj', 6.0, 3.9, 4.0, 2.9]] },
  F: { w: 12.0, d: 7.0, pokoje: [['salon', 0, 0, 6.0, 4.0], ['sypialnia', 6.0, 0, 3.4, 4.0], ['pokoj', 9.4, 0, 2.6, 4.0], ['pokoj', 0, 4.0, 3.2, 3.0], ['lazienka', 3.2, 4.0, 2.4, 3.0], ['przedpokoj', 5.6, 4.0, 4.0, 3.0], ['garderoba', 9.6, 4.0, 2.4, 3.0]] },
};

const GRANAT = '#0f2a3a';
const MORZE = '#1f6f78';
const liczba = (n, jezyk) => (jezyk === 'pl' ? n.toFixed(1).replace('.', ',') : n.toFixed(1));

function svg(typ, jezyk) {
  const { w, d, pokoje } = UKLADY[typ];
  const W = w * SKALA + 2 * MARGINES;
  const H = d * SKALA + 2 * MARGINES;
  const px = (m) => MARGINES + m * SKALA;

  const pomieszczenia = pokoje
    .map(([rodzaj, x, y, pw, pd]) => {
      const cx = px(x + pw / 2);
      const cy = px(y + pd / 2);
      const maly = pw < 2.7;
      const tlo = rodzaj === 'salon' ? '#f1ece0' : rodzaj === 'lazienka' ? '#e3eeee' : '#ffffff';
      return `
      <rect x="${px(x)}" y="${px(y)}" width="${pw * SKALA}" height="${pd * SKALA}" fill="${tlo}" stroke="${GRANAT}" stroke-width="4"/>
      <text x="${cx}" y="${cy - 4}" text-anchor="middle" font-size="${maly ? 19 : 23}" font-weight="600" fill="${GRANAT}">${NAZWY[rodzaj][jezyk]}</text>
      <text x="${cx}" y="${cy + 24}" text-anchor="middle" font-size="${maly ? 18 : 21}" fill="${MORZE}">${liczba(pw * pd, jezyk)} m²</text>`;
    })
    .join('');

  // Okna od strony rzeki: przerwy w górnej ścianie z podwójną linią.
  const okna = pokoje
    .filter(([, , y]) => y === 0)
    .map(([, x, , pw]) => {
      const szer = Math.min(pw * 0.55, 2.2) * SKALA;
      const x0 = px(x + pw / 2) - szer / 2;
      const y0 = px(0);
      return `<rect x="${x0}" y="${y0 - 6}" width="${szer}" height="12" fill="#fff"/>
      <line x1="${x0}" x2="${x0 + szer}" y1="${y0 - 4}" y2="${y0 - 4}" stroke="${GRANAT}" stroke-width="2"/>
      <line x1="${x0}" x2="${x0 + szer}" y1="${y0 + 4}" y2="${y0 + 4}" stroke="${GRANAT}" stroke-width="2"/>`;
    })
    .join('');

  const suma = pokoje.reduce((s, [, , , pw, pd]) => s + pw * pd, 0);

  return `<svg xmlns="http://www.w3.org/2000/svg" width="${W}" height="${H + 40}" viewBox="0 0 ${W} ${H + 40}" font-family="Inter, Arial, sans-serif">
    <rect width="100%" height="100%" fill="#ffffff"/>
    <g>
      <path d="M${px(0)} ${MARGINES - 46} q 30 -10 60 0 t 60 0 t 60 0" fill="none" stroke="${MORZE}" stroke-width="3" stroke-linecap="round"/>
      <text x="${px(0) + 200}" y="${MARGINES - 40}" font-size="20" font-style="italic" fill="${MORZE}">${NAZWY.rzeka[jezyk]} ↑</text>
    </g>
    ${pomieszczenia}
    <rect x="${px(0)}" y="${px(0)}" width="${w * SKALA}" height="${d * SKALA}" fill="none" stroke="${GRANAT}" stroke-width="10"/>
    ${okna}
    <text x="${W - MARGINES}" y="${H + 12}" text-anchor="end" font-size="22" font-weight="600" fill="${GRANAT}">${jezyk === 'pl' ? 'Razem' : 'Total'}: ${liczba(suma, jezyk)} m²</text>
  </svg>`;
}

const katalog = new URL('./seed-media/rzuty/', import.meta.url);
await mkdir(katalog, { recursive: true });

for (const typ of Object.keys(UKLADY)) {
  for (const jezyk of ['pl', 'en']) {
    const plik = new URL(`rzut-${typ}-${jezyk}.webp`, katalog);
    await sharp(Buffer.from(svg(typ, jezyk)), { density: 144 }).resize({ width: 1400, withoutEnlargement: true }).webp({ quality: 88 }).toFile(plik.pathname.replace(/^\/([A-Z]:)/, '$1'));
    const suma = UKLADY[typ].pokoje.reduce((s, [, , , pw, pd]) => s + pw * pd, 0);
    console.log(`rzut-${typ}-${jezyk}.webp  ${suma.toFixed(1)} m²`);
  }
}
await writeFile(new URL('metraze.json', katalog), JSON.stringify(Object.fromEntries(Object.entries(UKLADY).map(([t, u]) => [t, +u.pokoje.reduce((s, [, , , pw, pd]) => s + pw * pd, 0).toFixed(1)])), null, 2));
