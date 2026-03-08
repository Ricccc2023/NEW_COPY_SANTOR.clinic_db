<?php
declare(strict_types=1);

/**
 * Normalize Philippine phone numbers to +639XXXXXXXXX
 */
function normalize_ph_e164(string $input): string {

    $input = trim($input);
    if ($input === '') return '';

    if (str_starts_with($input, '+')) {
        return preg_replace('/(?!^\+)[^0-9]/', '', $input);
    }

    $digits = preg_replace('/[^0-9]/', '', $input);

    if (preg_match('/^09\d{9}$/', $digits)) return '+63' . substr($digits, 1);
    if (preg_match('/^9\d{9}$/',  $digits)) return '+63' . $digits;
    if (preg_match('/^639\d{9}$/',$digits)) return '+' . $digits;
    if (preg_match('/^63\d{10}$/',$digits)) return '+' . $digits;

    return '';
}