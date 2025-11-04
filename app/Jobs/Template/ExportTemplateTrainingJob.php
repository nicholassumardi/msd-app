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

class ExportTemplateTrainingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $cacheKey;

    public function __construct($cacheKey)
    {
        $this->cacheKey = $cacheKey;
    }

    public function handle()
    {
        $filepath = storage_path('app/public/temp/msd-template-training.xlsx');
        $writer  = new Writer();
        $writer->setCreator("MSD TEAM");
        $writer->openToFile($filepath);

        // 1st SHEET
        $styleHeader = new Style();
        $styleHeader->setBackgroundColor(Color::GRAY);
        $styleHeader->setFontBold();

        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Data Training');
        $sheet->setColumnWidthForRange(60, 1, 25);

        $row = Row::fromValues([
            'Nomor Training',
            'NIP Trainee',
            'No KTP',
            'NIP Trainee (HR Code)',
            'Nama Trainee',
            'Departemen',
            'Materi Pembelajaran IKW',
            'Nomor Revisi IKW',
            'NIP Trainer (INTIJI)',
            'No KTP (Trainer)',
            'NIP Trainee (HR Code) Trainer',
            'Nama Trainer (INTIJI)',
            'Tanggal Perencanaan Pengajaran (M/D/Y)',
            'Tanggal Realisasi Pengajaran (M/D/Y)',
            'Waktu Lama Training (Menit)',
            'Tanggal Pengembalian Tiket',
            'NIP Assessor (GUTEJI)',
            'No KTP (Assessor)',
            'NIP Trainee (HR Code) Assessor',
            'Nama Assesor (GUTEJI)',
            'Tanggal Rencana Assesment (M/D/Y)',
            'Tanggal Realisasi Assesment (M/D/Y)',
            'Waktu Lama Assesment (Menit)',
            'Hasil Assessment (K, BK, RK)',
            'Keterangan',
        ], $styleHeader);


        $writer->addRow($row);

        Cache::put($this->cacheKey, $filepath, now()->addMinutes(3));
        $writer->close();

        return $filepath;
    }
}
