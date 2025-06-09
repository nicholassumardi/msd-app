<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\Department;
use App\Models\User;
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

class ExportUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cacheKey;
    protected $user;
    protected $company;
    protected $department;

    public function __construct($cacheKey, $user)
    {
        $this->cacheKey = $cacheKey;
        $this->user = $user;
        $this->company =  Company::all();
        $this->department =  Department::all();
    }

    public function handle()
    {
        $filepath = storage_path('app/public/temp/msd-latest-data-karyawan.xlsx');
        $writer  = new Writer();
        $writer->setCreator("MSD TEAM");
        $writer->openToFile($filepath);
        // $writer->openToBrowser('msd-data-karyawan.xlsx');

        // 1st SHEET
        $styleHeader = new Style();
        $styleHeader->setFontBold();

        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Data Karyawan');
        $sheet->setColumnWidthForRange(23, 2, 40);
        $sheet->setColumnWidth(60, 7);
        $sheet->setColumnWidth(60, 13);

        $row = Row::fromValues([
            'No',
            'No Pegawai',
            'Nama',
            'PT',
            'DEPT',
            'TANGGAL LAHIR',
            'No KTP',
            'Unicode Data Diri',
            'Status (Aktif/ Non Aktif)',
            'Jenis Kelamin',
            'Agama',
            'Pendidikan',
            'Status Pernikahan',
            'Alamat',
            'No HP',
            'Klasifikasi',
            'Bagian',
            'Kode Posisi',
            'Kode Jabatan',
            'Grup',
            'Status Shift',
            'Status TWIJI',
            'Tanggal Masuk',
            'Tanggal Out',
            'Service Years (All)',
            'Service Years',
            'Masa Kerja',
            'Umur (Tahun Bulan)',
            'Umur (Tahun)',
            'Tahun',
            'Generasi',
        ], $styleHeader);

        $writer->addRow($row);


        foreach ($this->user->toArray() as $data) {
            $row = Row::fromValues($data);
            $writer->addRow($row);
        }

        Cache::put($this->cacheKey, $filepath, now()->addMinutes(10));
        $writer->close();

        return $filepath;
    }
}
