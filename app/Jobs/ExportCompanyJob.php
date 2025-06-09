<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;

class ExportCompanyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cacheKey;
    protected $company;
    protected $department;

    public function __construct($cacheKey, $company)
    {
        $this->cacheKey = $cacheKey;
        $this->company =  $company;
    }

    public function handle()
    {
        $filepath = storage_path('app/public/temp/msd-latest-data-pt.xlsx');
        $writer  = new Writer();
        $writer->setCreator("MSD TEAM");
        $writer->openToFile($filepath);

        // 1st SHEET
        $styleHeader = new Style();
        $styleHeader->setFontBold();

        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Data PT');
        $sheet->setColumnWidthForRange(23, 2, 40);
        $sheet->setColumnWidth(60, 7);
        $sheet->setColumnWidth(60, 13);

        $row = Row::fromValues([
            'No',
            'Nama PT',
            'Kode PT',
        ], $styleHeader);

        $writer->addRow($row);


        foreach ($this->company->toArray() as $data) {
            $row = Row::fromValues($data);
            $writer->addRow($row);
        }

        Cache::put($this->cacheKey, $filepath, now()->addMinutes(10));
        $writer->close();

        return $filepath;
    }
}
