<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Terapkan pencarian tanggal fleksibel pada query builder
     */
    protected function applyDateSearch($query, $column, $search)
    {
        $search = trim($search);
        if (empty($search)) {
            return;
        }

        // 1. Format: DD/MM/YYYY atau DD-MM-YYYY
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $search, $matches)) {
            $formatted = sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
            $query->orWhereDate($column, $formatted);
            return;
        }

        // 2. Format: YYYY-MM-DD atau YYYY/MM/DD
        if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', $search, $matches)) {
            $formatted = sprintf('%04d-%02d-%02d', $matches[1], $matches[2], $matches[3]);
            $query->orWhereDate($column, $formatted);
            return;
        }

        // 3. Format: DD/MM/YY atau DD-MM-YY
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2})$/', $search, $matches)) {
            $year = 2000 + intval($matches[3]);
            $formatted = sprintf('%04d-%02d-%02d', $year, $matches[2], $matches[1]);
            $query->orWhereDate($column, $formatted);
            return;
        }

        // 4. Nama Bulan Indonesia
        $months = [
            'januari' => '01', 'jan' => '01',
            'februari' => '02', 'feb' => '02',
            'maret' => '03', 'mar' => '03',
            'april' => '04', 'apr' => '04',
            'mei' => '05',
            'juni' => '06', 'jun' => '06',
            'juli' => '07', 'jul' => '07',
            'agustus' => '08', 'agu' => '08', 'agt' => '08',
            'september' => '09', 'sep' => '09',
            'oktober' => '10', 'okt' => '10',
            'november' => '11', 'nov' => '11',
            'desember' => '12', 'des' => '12',
        ];

        $lower = strtolower($search);
        foreach ($months as $name => $num) {
            // Contoh: "17 november 2026"
            if (preg_match('/^(\d{1,2})\s+' . $name . '\s+(\d{4})$/i', $lower, $m)) {
                $formatted = sprintf('%04d-%02d-%02d', $m[2], $num, $m[1]);
                $query->orWhereDate($column, $formatted);
                return;
            }
            // Contoh: "november 2026"
            if (preg_match('/^' . $name . '\s+(\d{4})$/i', $lower, $m)) {
                $query->orWhereRaw("DATE_FORMAT({$column}, '%Y-%m') = ?", [sprintf('%04d-%02d', $m[1], $num)]);
                return;
            }
            // Contoh: "17 november"
            if (preg_match('/^(\d{1,2})\s+' . $name . '$/i', $lower, $m)) {
                $query->orWhereRaw("DATE_FORMAT({$column}, '%m-%d') = ?", [sprintf('%02d-%02d', $num, $m[1])]);
                return;
            }
            // Hanya nama bulan
            if ($lower === $name) {
                $query->orWhereRaw("DATE_FORMAT({$column}, '%m') = ?", [$num]);
                return;
            }
        }

        // 5. Cek tahun 4 digit (misal: "2026")
        if (preg_match('/^(19|20)\d{2}$/', $search)) {
            $query->orWhereRaw("DATE_FORMAT({$column}, '%Y') = ?", [$search]);
            return;
        }

        // 6. Pencarian substring tanggal umum
        $query->orWhereRaw("DATE_FORMAT({$column}, '%d/%m/%Y') LIKE ?", ["%{$search}%"])
              ->orWhereRaw("DATE_FORMAT({$column}, '%d-%m-%Y') LIKE ?", ["%{$search}%"])
              ->orWhereRaw("DATE_FORMAT({$column}, '%Y-%m-%d') LIKE ?", ["%{$search}%"]);
    }

    /**
     * Normalisasi input filter tanggal menjadi YYYY-MM-DD
     */
    protected function normalizeDateFilter($dateInput)
    {
        if (empty($dateInput)) {
            return null;
        }
        $dateInput = trim($dateInput);
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $dateInput, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateInput)) {
            return $dateInput;
        }
        $timestamp = strtotime($dateInput);
        return $timestamp ? date('Y-m-d', $timestamp) : $dateInput;
    }
}
