<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CpfCnpj implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $numbers = preg_replace('/\D/', '', $value);

        if (strlen($numbers) === 11) {
            if (! self::validCpf($numbers)) {
                $fail('O CPF informado é inválido.');
            }
        } elseif (strlen($numbers) === 14) {
            if (! self::validCnpj($numbers)) {
                $fail('O CNPJ informado é inválido.');
            }
        } else {
            $fail('Informe um CPF (11 dígitos) ou CNPJ (14 dígitos) válido.');
        }
    }

    public static function validCpf(string $cpf): bool
    {
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $sum = 0;
            for ($i = 0; $i < $t; $i++) {
                $sum += (int) $cpf[$i] * (($t + 1) - $i);
            }
            $digit = ((10 * $sum) % 11) % 10;
            if ((int) $cpf[$t] !== $digit) {
                return false;
            }
        }

        return true;
    }

    public static function validCnpj(string $cnpj): bool
    {
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $cnpj[$i] * $weights1[$i];
        }
        $digit1 = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);
        if ((int) $cnpj[12] !== $digit1) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += (int) $cnpj[$i] * $weights2[$i];
        }
        $digit2 = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);
        if ((int) $cnpj[13] !== $digit2) {
            return false;
        }

        return true;
    }
}
