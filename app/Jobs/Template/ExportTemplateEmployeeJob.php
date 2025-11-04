<?php

namespace App\Jobs\Template;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;

class ExportTemplateEmployeeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cacheKey;
    public function __construct($cacheKey)
    {
        $this->cacheKey = $cacheKey;
    }

    public function handle()
    {
        $filepath = storage_path('app/public/temp/msd-template-data-karyawan.xlsx');
        $writer  = new Writer();
        $writer->setCreator("MSD TEAM");
        $writer->openToFile($filepath);
        // $writer->openToBrowser('msd-data-karyawan.xlsx');

        // 1st SHEET
        $styleHeader = new Style();
        $styleHeader->setBackgroundColor(Color::GRAY);
        $styleHeader->setFontBold();

        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Data Karyawan');
        $sheet->setColumnWidthForRange(23, 2, 40);
        $sheet->setColumnWidth(60, 7);
        $sheet->setColumnWidth(60, 13);

        $row = Row::fromValues([
            'No',
            'Nomor Pegawai',
            'Nama',
            'PT',
            'DEPT',
            'Tanggal Lahir',
            'No KTP',
            'Unicode Data Diri',
            'Status (Aktif / Non Aktif)',
            'Check Data',
            'Nopeg BARU',
            'Jenis Kelamin',
            'Agama',
            'Pendidikan',
            'Status Pernikahan',
            'Alamat',
            'No HP',
            'Klasifikasi',
            'Bagian (P)',
            'Kode Posisi',
            'Kode Jabatan',
            'Unicode KPKJ',
            'Grup',
            'Status Shift',
            'Status Tunjangan',
            'Status TWIJI',
            'Sertifikasi Internal Auditor',
            'Sertifikasi Umum',
            'Sertifikasi Kendaraan & Civil',
            'Sertifikasi Tanggap Darurat',
            'Tanggal Masuk (Awal)',
            'Tanggal Out',
            'Tanggal Update',
            'Ket',
            'STATUS',
            'OS',
            'Status TK',
            'TK',
            'Staff/Non Staff',
            'Service Years (ALL)',
            'Service Years',
            'Masa Kerja',
            'MK (@5 Tahun)',
            'Umur (Tahun Bulan)',
            'Umur (Tahun)',
            'Umur Pekerja',
            'Tahun',
            'KG',
            'Jabatan',
            'PEH',
            'Nopeg2',
            'Nama Pegawai',
            'KTP',
            'KP',
            'KK',
            'Kkel',
            'Prov',
            'Kota',
            'Kel',
            'Pulau',
            'Kroscek data MCU',
        ], $styleHeader);


        $writer->addRow($row);


        Cache::put($this->cacheKey, $filepath, now()->addMinutes(3));
        $writer->close();

        return $filepath;
    }
}
