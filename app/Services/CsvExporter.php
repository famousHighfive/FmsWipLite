<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporter
{
    /**
     * Exporte une collection en fichier CSV.
     */
    public function export(Collection $collection, string $filename): StreamedResponse
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($collection) {
            $file = fopen('php://output', 'w');
            
            // En-têtes basés sur les clés du premier élément
            if ($collection->isNotEmpty()) {
                $firstItem = $collection->first();
                $keys = array_keys($firstItem instanceof \Illuminate\Database\Eloquent\Model ? $firstItem->getAttributes() : (array)$firstItem);
                fputcsv($file, $keys);

                foreach ($collection as $item) {
                    $row = $item instanceof \Illuminate\Database\Eloquent\Model ? $item->getAttributes() : (array)$item;
                    fputcsv($file, $row);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
