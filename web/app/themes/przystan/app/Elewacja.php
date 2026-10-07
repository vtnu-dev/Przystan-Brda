<?php

namespace App;

/**
 * Geometria rysowanej elewacji: zamienia piętro i pozycję mieszkania na prostokąt w SVG.
 * Rysunek powstaje z danych WordPressa, więc zmiana statusu w panelu od razu zmienia kolor okna.
 */
final class Elewacja
{
    public const SZEROKOSC = 1200;
    public const KOLUMNY = 6;
    public const PIETRA = 6; // parter + 5 pięter

    public const X0 = 92;
    public const X1 = 1150;
    public const DACH = 34;
    public const GORA = 84;
    public const WYS_PIETRA = 94;
    public const WYS_OKNA = 62;
    public const MARGINES_OKNA = 13;

    public static function dol(): int
    {
        return self::GORA + self::PIETRA * self::WYS_PIETRA;
    }

    public static function wysokosc(): int
    {
        return self::dol() + 104;
    }

    public static function szerokoscKolumny(): float
    {
        return (self::X1 - self::X0) / self::KOLUMNY;
    }

    /** Górna krawędź kondygnacji (piętro 5 na górze, parter na dole). */
    public static function yPietra(int $pietro): int
    {
        return self::GORA + (self::PIETRA - 1 - $pietro) * self::WYS_PIETRA;
    }

    /**
     * @param  array<string, mixed>  $m  mieszkanie z Mieszkanie::zPosta()
     * @return array{x: float, y: float, w: float, h: float, cx: float, cy: float}
     */
    public static function okno(array $m): array
    {
        $kolumna = max(1, min(self::KOLUMNY, (int) $m['pozycja'])) - 1;
        $x = self::X0 + $kolumna * self::szerokoscKolumny() + self::MARGINES_OKNA;
        $w = self::szerokoscKolumny() - 2 * self::MARGINES_OKNA;
        $y = self::yPietra((int) $m['pietro']) + 14;

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
