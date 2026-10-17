/**
 * Telefon, strona główna: stały pasek „Znajdź mieszkanie · N wolnych”. Widoczny dopiero po zjechaniu z hero
 * (tam jest ten sam przycisk), chowa się przy bloku kontaktu i w stopce, żeby nie zasłaniał formularza ani linków.
 * Body dostaje dolny odstęp (klasa z-paskiem), więc pasek nie przykrywa końca treści.
 */
export function pasekTelefon() {
  const pasek = document.querySelector('[data-pasek-telefon]');
  if (!pasek || !('IntersectionObserver' in window)) return;
  pasek.hidden = false;
  pasek.classList.add('schowany');
  document.body.classList.add('z-paskiem');

  const widoczne = new Set();
  const cele = [document.querySelector('[data-hero]'), document.querySelector('[aria-labelledby="cta-tytul"]'), document.querySelector('footer')].filter(Boolean);
  const obs = new IntersectionObserver((wpisy) => {
    wpisy.forEach((w) => (w.isIntersecting ? widoczne.add(w.target) : widoczne.delete(w.target)));
    const schowaj = widoczne.size > 0;
    pasek.classList.toggle('schowany', schowaj);
    pasek.inert = schowaj;
  });
  cele.forEach((c) => obs.observe(c));
}
