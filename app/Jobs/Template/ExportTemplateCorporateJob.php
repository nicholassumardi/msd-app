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

class ExportTemplateCorporateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cacheKey;
    public function __construct($cacheKey)
    {
        $this->cacheKey = $cacheKey;
    }

    public function handle()
    {
        $filepath = storage_path('app/public/temp/msd-template-data-pt.xlsx');
        $writer  = new Writer();
        $writer->setCreator("MSD TEAM");
        $writer->openToFile($filepath);

        // 1st SHEET
        $styleHeader = new Style();
        $styleHeader->setBackgroundColor(Color::GRAY);
        $styleHeader->setFontBold();

        $sheet = $writer->getCurrentSheet();
        $sheet->setName('PT');
        $sheet->setColumnWidthForRange(23, 2, 40);
        $sheet->setColumnWidth(60, 7);
        $sheet->setColumnWidth(60, 13);

        $row = Row::fromValues([
            'Nama',
            'Unique Code',
            'PT (Singkatan)',
        ], $styleHeader);

        $writer->addRow($row);

        // 2nd SHEET
        $styleHeaderNewSheet = new Style();
        $styleHeaderNewSheet->setBackgroundColor(Color::GRAY);
        $styleHeaderNewSheet->setFontBold();

        $newSheet = $writer->addNewSheetAndMakeItCurrent();
        $newSheet->setName('DEPT');
        $newSheet->setColumnWidthForRange(23, 1, 6);

        $row = Row::fromValues(['PT (Singkatan)', 'NAMA', 'DEPT'], $styleHeaderNewSheet);
        $writer->addRow($row);

        Cache::put($this->cacheKey, $filepath, now()->addMinutes(3));
        $writer->close();

        return $filepath;
    }
}
