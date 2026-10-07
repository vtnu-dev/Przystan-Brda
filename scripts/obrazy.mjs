/**
 * Zdjęcia z Google Flow (_robocze/flow/*.png, poza repo) -> lekkie JPG do seeda (scripts/seed-media/).
 * WordPress przy imporcie robi z nich WebP w kilku rozmiarach (filtr w motywie).
 *
 * Uruchomienie: npm run obrazy
 */
import { mkdir } from 'node:fs/promises';
import sharp from 'sharp';

const PLIKI = {
  'hero-zmierzch-1': 'hero-zmierzch',
  'dzien-bulwar': 'bulwar',
  'wnetrze-salon': 'salon',
  'wnetrze-kuchnia': 'kuchnia',
  'wnetrze-sypialnia': 'sypialnia',
  'budowa-1-fundamenty': 'budowa-fundamenty',
  'budowa-2-stan-surowy': 'budowa-stan-surowy',
  'budowa-3-elewacja': 'budowa-elewacja',
  'budowa-4-bulwar': 'budowa-bulwar',
  'lipowa-elewacja': 'lipowa',
  'lipowa-wnetrze': 'lipowa-wnetrze',
  'myslecinek-wizualizacja': 'myslecinek',
};

const sciezka = (url) => decodeURIComponent(url.pathname).replace(/^\/([A-Za-z]:)/, '$1');
const zrodlo = new URL('../_robocze/flow/', import.meta.url);
const cel = new URL('./seed-media/', import.meta.url);
await mkdir(cel, { recursive: true });

for (const [wejscie, wyjscie] of Object.entries(PLIKI)) {
  const info = await sharp(sciezka(new URL(`${wejscie}.png`, zrodlo)))
    .resize({ width: 1920, withoutEnlargement: true })
    .jpeg({ quality: 84, mozjpeg: true, progressive: true })
    .toFile(sciezka(new URL(`${wyjscie}.jpg`, cel)));
  console.log(`${wyjscie}.jpg  ${info.width}x${info.height}  ${Math.round(info.size / 1024)} KB`);
}

// Obraz do udostępniania (Open Graph) 1200x630 z hero.
const og = await sharp(sciezka(new URL('hero-zmierzch-1.png', zrodlo)))
  .resize(1200, 630, { fit: 'cover', position: 'centre' })
  .jpeg({ quality: 82, mozjpeg: true })
  .toFile(sciezka(new URL('og.jpg', cel)));
console.log(`og.jpg  ${og.width}x${og.height}  ${Math.round(og.size / 1024)} KB`);
