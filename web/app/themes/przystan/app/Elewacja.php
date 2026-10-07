<?php

namespace App;

/**
 * Geometria rysowanej elewacji: zamienia piętro i pozycję mieszkania na prostokąt w SVG.
 * Liczba kondygnacji i lokali na piętro pochodzi z pól inwestycji, więc ten sam komponent
 * rysuje każdy budynek, a zmiana statusu w panelu od razu zmienia kolor okna.
 */
final class Elewacja
{
    public const SZEROKOSC = 1200;
    public const X0 = 92;
    public const X1 = 1150;
    public const DACH = 34;
    public const GORA = 84;
    public const WYS_PIETRA = 94;
    public const WYS_OKNA = 62;
    public const MARGINES_OKNA = 13;

    public function __construct(
        public readonly int $kondygnacje = 6,
        public readonly int $kolumny = 6,
    ) {}

    /** Elewacja według pól inwestycji (domyślnie parter + 5 pięter, 6 lokali). */
    public static function dlaInwestycji(int $inwestycjaId): self
    {
        $kondygnacje = (int) get_post_meta($inwestycjaId, 'kondygnacje', true);
        $kolumny = (int) get_post_meta($inwestycjaId, 'lokali_na_pietro', true);

        return new self(max(1, $kondygnacje ?: 6), max(1, $kolumny ?: 6));
    }

    public function dol(): int
    {
        return self::GORA + $this->kondygnacje * self::WYS_PIETRA;
    }

    public function wysokosc(): int
    {
        return $this->dol() + 104;
    }

    public function szerokoscKolumny(): float
    {
        return (self::X1 - self::X0) / $this->kolumny;
    }

    /** Górna krawędź kondygnacji (najwyższe piętro na górze, parter na dole). */
    public function yPietra(int $pietro): int
    {
        return self::GORA + ($this->kondygnacje - 1 - $pietro) * self::WYS_PIETRA;
    }

    /**
     * @param  array<string, mixed>  $m  mieszkanie z Mieszkanie::zPosta()
     * @return array{x: float, y: float, w: float, h: float, cx: float, cy: float}
     */
    public function okno(array $m): array
    {
        $kolumna = max(1, min($this->kolumny, (int) $m['pozycja'])) - 1;
        $pietro = max(0, min($this->kondygnacje - 1, (int) $m['pietro']));
        $x = self::X0 + $kolumna * $this->szerokoscKolumny() + self::MARGINES_OKNA;
        $w = $this->szerokoscKolumny() - 2 * self::MARGINES_OKNA;
        $y = $this->yPietra($pietro) + 14;

        return [
            'x' => round($x, 1),
            'y' => $y,
            'w' => round($w, 1),
            'h' => self::WYS_OKNA,
            'cx' => round($x + $w / 2, 1),
            'cy' => $y + self::WYS_OKNA / 2 + 6,
        ];
    }

    public static function etykietaPietra(int $pietro): string
    {
        return $pietro === 0 ? _x('P', 'skrót: parter na elewacji', 'przystan') : (string) $pietro;
    }
}
