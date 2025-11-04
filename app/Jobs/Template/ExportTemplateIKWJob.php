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

class ExportTemplateIKWJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cacheKey;

    public function __construct($cacheKey)
    {
        $this->cacheKey = $cacheKey;
    }

    public function handle()
    {

        $filepath = storage_path('app/public/temp/msd-template-ikw.xlsx');
        $writer  = new Writer();
        $writer->setCreator("MSD TEAM");
        $writer->openToFile($filepath);

        // 1st SHEET
        $styleHeader = new Style();
        $styleHeader->setBackgroundColor(Color::GRAY);
        $styleHeader->setFontBold();

        $sheet = $writer->getCurrentSheet();
        $sheet->setName('IKW');
        $sheet->setColumnWidthForRange(23, 2, 40);
        $sheet->setColumnWidth(60, 7);
        $sheet->setColumnWidth(60, 13);
        $row = Row::fromValues([
            "Kode Dept",
            "No WIN",
            "Nama IKW",
            "No Rev",
            "JumLah Halaman",
            "Tanggal Daftar",
            "Tanggal Meeting 1",
            "Durasi Meeting 1 (Menit)",
            "OK/NOK/ Revisi/Hapus",
            "Tanggal Meeting 2",
            "Durasi Meeting 2 (Menit)",
            "OK/NOK/ Revisi/Hapus2",
            "Tanggal Meeting 3",
            "Durasi Meeting 3 (Menit)",
            "OK/NOK/ Revisi/Hapus3",
            "dicetak oleh Back Office",
            "Tanggal Penyerahan ke dept",
            "Tanggal Pengembalian IKW",
            "Position Call Number",
            "Pelaksana Lapangan",
            "Position Call Number2",
            "Pelaksana Lapangan2",
            "Position Call Number3",
            "Pelaksana Lapangan3",
            "Position Call Number4",
            "Pelaksana Lapangan4",
            "Position Call Number5",
            "Pelaksana Lapangan5",
            "Durasi Pembuatan IKW",
            "Status Dokumen",
            "Tanggal Update Terakhir",
            "keterangan"
        ], $styleHeader);

        $writer->addRow($row);

        Cache::put($this->cacheKey, $filepath, now()->addMinutes(3));
        $writer->close();

        return $filepath;
    }
}
