<?php

namespace App\Exports;

use App\Models\Dosen;
use App\Models\Presensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapPresensiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $prodiId;
    protected $month;
    protected $year;

    public function __construct($prodiId = null, $month = null, $year = null)
    {
        $this->prodiId = $prodiId;
        $this->month = $month ?? now()->month;
        $this->year = $year ?? now()->year;
    }

    public function collection()
    {
        $query = Dosen::with(['programStudi']);

        if ($this->prodiId) {
            $query->where('program_studi_id', $this->prodiId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nama Dosen',
            'NIDN',
            'Program Studi',
            'Total Hadir',
            'Terlambat',
            'Izin/Sakit',
            'Persentase (%)'
        ];
    }

    public function map($dosen): array
    {
        $hadir = Presensi::where('dosen_id', $dosen->id)
            ->where('status', 'hadir')
            ->whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->count();

        $terlambat = Presensi::where('dosen_id', $dosen->id)
            ->where('status', 'terlambat')
            ->whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->count();

        $izin = Presensi::where('dosen_id', $dosen->id)
            ->whereIn('status', ['izin', 'sakit'])
            ->whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->count();

        // Assumption: 16 pertemuan per semester, roughly 4 per month
        $totalTarget = 16 / 4; // Simple estimation for demo
        $persentase = $totalTarget > 0 ? round((($hadir + $terlambat) / $totalTarget) * 100, 1) : 0;

        return [
            $dosen->nama_gelar,
            $dosen->nidn,
            $dosen->programStudi->nama_prodi ?? '-',
            $hadir,
            $terlambat,
            $izin,
            $persentase . '%'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E7D1B1']]],
        ];
    }
}
