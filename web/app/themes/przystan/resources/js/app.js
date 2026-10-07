import.meta.glob(['../images/**', '../fonts/**']);

import { menu } from './modules/menu.js';
import { elewacje } from './modules/elewacja.js';
import { wyszukiwarka } from './modules/wyszukiwarka.js';
import { formularze } from './modules/formularz.js';
import { ulubione } from './modules/ulubione.js';
import { kalkulatory } from './modules/kalkulator.js';
import { porownanie } from './modules/porownanie.js';
import { ruch, przejscia } from './modules/ruch.js';
import { hero } from './modules/hero.js';

menu();
elewacje();
wyszukiwarka();
formularze();
ulubione();
kalkulatory();
porownanie();
ruch();
przejscia();
hero();
