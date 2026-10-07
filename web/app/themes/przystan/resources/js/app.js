import.meta.glob(['../images/**', '../fonts/**']);

import { menu } from './modules/menu.js';
import { elewacje } from './modules/elewacja.js';
import { wyszukiwarka } from './modules/wyszukiwarka.js';
import { formularze } from './modules/formularz.js';

menu();
elewacje();
wyszukiwarka();
formularze();
