<?php

if (!function_exists('formatTanggalIndonesia')) {
    function formatTanggalIndonesia(string $value, bool $includeTime = false): string
    {
        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return '';
        }

        $date = date($includeTime ? 'd F Y, H:i' : 'd F Y', $timestamp);
        return strtr($date, [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember',
        ]);
    }
}
