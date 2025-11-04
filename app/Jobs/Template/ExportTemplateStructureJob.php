<?php

namespace App\Jobs\Template;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;

class ExportTemplateStructureJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $cacheKey;

    public function __construct($cacheKey)
    {
        $this->cacheKey = $cacheKey;
    }

    public function handle()
    {
        $filepath = storage_path('app/public/temp/msd-data-lo.xlsx');
        $writer  = new Writer();
        $writer->setCreator("MSD TEAM");
        $writer->openToFile($filepath);

        // 1st SHEET
        $styleHeader = new Style();
        $styleHeader->setBackgroundColor(Color::GRAY);
        $styleHeader->setFontBold();

        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Data Plot LO');
        $sheet->setColumnWidthForRange(23, 1, 25);
        $sheet->setColumnWidth(60, 13);

        $row = Row::fromValues([
            'PT',
            'DEPT',
            'ID STRUKTUR ATASAN',
            'KODE JABATAN ATASAN',
            'KODE POSISI ATASAN',
            'KODE GROUP ATASAN',
            'KODE SUFFIX',
            'KODE IP ATASAN',
            'SUB POSISI ATASAN',
            'ID STRUKTUR',
            'KODE JABATAN',
            'KODE POSISI',
            'KODE GROUP',
            'KODE SUFFIX',
            'KODE IP',
            'SUB POSISI',
            'ID STAFF',
            'NIP',
            'NIP BARU',
            'No KTP',
            'NAMA',
            'TGL NON AKTIF',
            'LEVEL'
        ], $styleHeader);

        $writer->addRow($row);

        Cache::put($this->cacheKey, $filepath, now()->addMinutes(3));
        $writer->close();

        return $filepath;
    }
}
