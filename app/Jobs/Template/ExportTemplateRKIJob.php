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

class ExportTemplateRKIJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cacheKey;

    public function __construct($cacheKey)
    {
        $this->cacheKey = $cacheKey;
    }

    public function handle()
    {
        $filepath = storage_path('app/public/temp/msd-template-RKI.xlsx');
        $writer  = new Writer();
        $writer->setCreator("MSD TEAM");
        $writer->openToFile($filepath);

        // 1st SHEET
        $styleHeader = new Style();
        $styleHeader->setBackgroundColor(Color::BEIGE);
        $styleHeader->setFontBold();

        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Data Training');
        $sheet->setColumnWidthForRange(60, 1, 25);

        $row = Row::fromValues([
            'Uniq Code Materi Training IKW',
            'Kode Jabatan - Kode Posisi',
            '',
            'No IKW',
            'Nama IKW',
            'Jumlah Halaman',
            'Waktu Pengajaran',
            'Nomor Modul',
            'Kode Dept',
        ], $styleHeader);



        $writer->addRow($row);

        Cache::put($this->cacheKey, $filepath, now()->addMinutes(3));
        $writer->close();

        return $filepath;
    }
}
