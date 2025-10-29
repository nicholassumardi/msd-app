<?php

namespace App\Jobs;

use App\Models\JobCode;
use App\Models\Structure;
use App\Models\User;
use App\Models\UserEmployeeNumber;
use App\Models\UserPlot;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;

class ImportUserPlotJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filepath;
    protected $cacheKey;
    protected $user;
    protected $jobCode;
    protected $structure;

    public function __construct($filepath, $cacheKey)
    {
        $this->filepath = $filepath;
        $this->cacheKey = $cacheKey;
        $this->user = User::all();
        $this->jobCode = JobCode::all();
        $this->structure = Structure::all();
    }

    public function handle()
    {
        try {
            DB::beginTransaction();

            $reader = new Reader();
            $reader->open(storage_path('app/public/' . $this->filepath));
            $dataUserPlot = [];
            $dataUserNotFound = [];
            $dataChunk = 200;

            foreach ($reader->getSheetIterator() as $i => $sheet) {
                if ($sheet->getName() == 'Database Struktur') {
                    foreach ($sheet->getRowIterator() as $key => $row) {
                        if ($key != 1 && $key != 2) {
                            $user = $this->findDataUser($row->getCells()[12]->getValue());
                            $userEmployeeNumber = $this->findDataByEmployeeNumber($row->getCells()[11]->getValue());

                            if (!$userEmployeeNumber || !$user) {
                                $dataUserNotFound[] = $row;
                            }

                            $jobCode = $this->jobCode->firstWhere('code', $row->getCells()[5]->getValue()) ? $this->jobCode->firstWhere('code', $row->getCells()[5]->getValue())->id : NULL;
                            $structure = $this->structure->firstWhere('name', $row->getCells()[8]->getValue()) ? $this->structure->firstWhere('name', $row->getCells()[8]->getValue())->id : NULL;

                            $dataUserPlot[] = [
                                'user_id'                       => $userEmployeeNumber || $user ? $user->id : NULL,
                                'job_code_id'                   => $jobCode,
                                'user_structure_mapping_id'     => $structure,
                                'id_structure'                  => $row->getCells()[9]->getValue(),
                                'id_staff'                      => $row->getCells()[10]->getValue(),
                                'position_code_structure'       => $row->getCells()[6]->getValue(),
                                'group'                         => $row->getCells()[7]->getValue(),
                                'assign_date'                   => Carbon::today()->format('Y-m-d'),
                                'reassign_date'                 => NULL,
                                'status'                        => 1,
                            ];

                            if (count($dataUserPlot) == $dataChunk) {
                                $this->insertChunk($dataUserPlot);
                                $dataUserPlot = [];
                            }
                        }
                    }

                    if (count($dataUserPlot) != 0) {
                        $this->insertChunk($dataUserPlot);
                    }
                }
            }

            $reader->close();
            Storage::delete($this->filepath);


            DB::commit();

            return true;
        } catch (\Exception $ex) {
            DB::rollBack();
            return false;
        }
    }


    public function insertChunk($dataUserPlot)
    {
        UserPlot::insert($dataUserPlot);
    }

    public function findDataByEmployeeNumber($search)
    {
        return UserEmployeeNumber::where('employee_number', $search)
            ->where('status', 1)
            ->first();
    }

    public function findDataUser($search)
    {
        return User::where('employee')->orWhere(DB::raw('LOWER(name)'), '=', strtolower($search))
            ->first();
    }

    // public function exportData($dataUserNotFound)
    // {
    //     $filepath = storage_path('app/public/temp/msd-data-lo.xlsx');
    //     $writer  = new Writer();
    //     $writer->setCreator("MSD TEAM");
    //     $writer->openToFile($filepath);

    //     // 1st SHEET
    //     $style = new Style();
    //     $style->setBackgroundColor(Color::DARK_BLUE);
    //     $styleHeader = new Style();
    //     $styleHeader->setBackgroundColor(Color::DARK_BLUE);
    //     $styleHeader->setFontBold();

    //     $sheet = $writer->getCurrentSheet();
    //     $sheet->setName('Missing Data Karyawan');
    //     $sheet->setColumnWidthForRange(23, 1, 25);
    //     $sheet->setColumnWidth(60, 13);

    //     $row = Row::fromValues([
    //         'PT',
    //         'DEPT',
    //         'ID STRUKTUR ATASAN',
    //         'KODE JABATAN ATASAN',
    //         'KODE POSISI ATASAN',
    //         'KODE GROUP ATASAN',
    //         'KODE SUFFIX',
    //         'KODE IP ATASAN',
    //         'SUB POSISI ATASAN',
    //         'ID STRUKTUR',
    //         'KODE JABATAN',
    //         'KODE POSISI',
    //         'KODE GROUP',
    //         'KODE SUFFIX',
    //         'KODE IP',
    //         'SUB POSISI',
    //         'ID STAFF',
    //         'NIP',
    //         'NAMA',
    //         'TGL NON AKTIF',
    //         'LEVEL'
    //     ], $styleHeader);

    //     $writer->addRow($row);

    //     foreach ($dataUserNotFound as $data) {
    //         $row = Row::fromValues($data, $style);
    //         $writer->addRow($row);
    //     }

    //     $writer->close();

    //     return $filepath;
    // }
}
