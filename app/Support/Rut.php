<?php

namespace App\Support;

class Rut
{
    /**
     * Strip dots, hyphen and spaces, and upper-case the verifier digit.
     */
    public static function clean(string $rut): string
    {
        return strtoupper(preg_replace('/[^0-9kK]/', '', $rut) ?? '');
    }

    /**
     * Split a RUT into its numeric body and verifier digit.
     *
     * @return array{0: string, 1: string}|null
     */
    public static function split(string $rut): ?array
    {
        $clean = self::clean($rut);

        if (! preg_match('/^(\d+)([0-9K])$/', $clean, $matches)) {
            return null;
        }

        return [$matches[1], $matches[2]];
    }

    /**
     * Compute the verifier digit for a numeric RUT body.
     */
    public static function verifierDigit(string $body): string
    {
        $sum = 0;
        $multiplier = 2;

        foreach (array_reverse(str_split($body)) as $digit) {
            $sum += (int) $digit * $multiplier;
            $multiplier = $multiplier === 7 ? 2 : $multiplier + 1;
        }

        $remainder = 11 - ($sum % 11);

        return match ($remainder) {
            11 => '0',
            10 => 'K',
            default => (string) $remainder,
        };
    }

    /**
     * Determine whether a RUT has a valid verifier digit.
     */
    public static function isValid(string $rut): bool
    {
        $parts = self::split($rut);

        if ($parts === null) {
            return false;
        }

        [$body, $verifier] = $parts;

        if (ltrim($body, '0') === '') {
            return false;
        }

        return self::verifierDigit($body) === $verifier;
    }

    /**
     * Format a RUT as 12.345.678-9.
     */
    public static function format(string $rut): ?string
    {
        $parts = self::split($rut);

        if ($parts === null) {
            return null;
        }

        [$body, $verifier] = $parts;

        return number_format((int) $body, 0, '', '.').'-'.$verifier;
    }

    /**
     * Mask a RUT for UI display only, e.g. 12.***.**8-9.
     *
     * This is presentation obfuscation, not a protection technique: a masked
     * RUT is reconstructible. Never use it for logs or storage; log internal
     * identifiers instead.
     */
    public static function mask(string $rut): ?string
    {
        $formatted = self::format($rut);

        if ($formatted === null) {
            return null;
        }

        return preg_replace_callback('/^(.{2})(.+)(.-.)$/', function (array $matches): string {
            return $matches[1].preg_replace('/\d/', '*', $matches[2]).$matches[3];
        }, $formatted);
    }
}
