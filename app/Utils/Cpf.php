<?php

namespace App\Utils;

class Cpf
{
    public static function validate(string $cpf): bool
    {
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );

        if (strlen($cpf) !== 11) {
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    public static function generate(): string
    {
        $baseCPF = sprintf("%09d", mt_rand(1, 999999999));

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $baseCPF[$i] * (10 - $i);
        }
        $digit1 = $sum % 11;
        $digit1 = ($digit1 < 2) ? 0 : 11 - $digit1;

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $baseCPF[$i] * (11 - $i);
        }
        $sum += $digit1 * 2;
        $digit2 = $sum % 11;
        $digit2 = ($digit2 < 2) ? 0 : 11 - $digit2;

        $generatedCPF = $baseCPF . $digit1 . $digit2;

        return $generatedCPF;
    }
}
