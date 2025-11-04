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

class ExportTemplateJobCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cacheKey;

    public function __construct($cacheKey)
    {
        $this->cacheKey = $cacheKey;
    }

    public function handle()
    {
        $filepath = storage_path('app/public/temp/msd-template-peh.xlsx');
        $writer  = new Writer();
        $writer->setCreator("MSD TEAM");
        $writer->openToFile($filepath);

        // 1st SHEET
        $styleHeader = new Style();
        $styleHeader->setBackgroundColor(Color::BEIGE);
        $styleHeader->setFontBold();

        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Data Training');
        $sheet->setColumnWidthForRange(25, 1, 6);
        $sheet->setColumnWidth(60, 7);
        $sheet->setColumnWidth(25, 8);

        $row = Row::fromValues([
            "Org Level",
            "JF",
            "Code",
            "JF Code",
            "Kode Jabatan",
            "Kategori",
            "Jabatan",
            "Level",
            "Struktur"
        ], $styleHeader);



        $writer->addRow($row);

        Cache::put($this->cacheKey, $filepath, now()->addMinutes(3));
        $writer->close();

        return $filepath;
    }
}
