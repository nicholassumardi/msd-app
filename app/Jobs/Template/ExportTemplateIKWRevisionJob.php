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

class ExportTemplateIKWRevisionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cacheKey;

    public function __construct($cacheKey)
    {
        $this->cacheKey = $cacheKey;
    }


    public function handle()
    {

        $filepath = storage_path('app/public/temp/msd-template-revisiIKW.xlsx');
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
            "No",
            "UNICODE",
            "DEPT",
            "No Dokumen",
            "No Rev (naik)",
            "Alasan Perubahan / Data Cancel",
            "Status IKW (pending/cancel)",
            "Status IKW Fix",
            "Konfirmasi",
            "Uraian Batal Revisi/ Hapus / Khusus",
            "No Pengajuan",
            "Tgl Terima Pengajuan",
            "Tgl Pengajuan ke MR",
            "Tgl Pengembalian Ke Back Office\n(Update Database Training)", // Hati-hati dengan newline character di sini
            "Major/Minor",
            "Tgl Cetak",
            "Tgl Serah",
            "Tgl TTD MR",
            "Tgl Distribusi",
            "Tgl Pengembalian Dok",
            "Tgl pemusnahan dokumen",
            "Keterangan (Posisi Dokumen)",
            "KET",
            "unik",
            "Status IKW Fix",
            "CEK"
        ], $styleHeader);

        $writer->addRow($row);

        Cache::put($this->cacheKey, $filepath, now()->addMinutes(3));
        $writer->close();

        return $filepath;
    }
}
