<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\StreamedResponse;

class Csv
{
    // Nagda-download ng CSV na mabubuksan sa Excel
    public static function download(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $out = fopen('php://output', 'w');

            fwrite($out, "\xEF\xBB\xBF"); // BOM para tama ang pagbasa ng Excel sa UTF-8
            fputcsv($out, $headers, ',', '"', '');

            foreach ($rows as $row) {
                fputcsv($out, array_map([self::class, 'safe'], $row), ',', '"', '');
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // Proteksyon laban sa "CSV injection": ang cell na nagsisimula sa = + - @ ay hindi isasagawa ng Excel bilang formula
    private static function safe(mixed $value): mixed
    {
        if (is_string($value) && preg_match('/^[=+\-@\t\r]/', $value)) {
            return "'" . $value;
        }

        return $value;
    }
}
