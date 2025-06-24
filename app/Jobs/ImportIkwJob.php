<?php

namespace App\Jobs;

use App\Models\Department;
use App\Models\IKW;
use App\Models\IkwMeeting;
use App\Models\IkwPosition;
use App\Models\IKWRevision;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use OpenSpout\Reader\XLSX\Reader;

class ImportIkwJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filepath;
    protected $department;
    protected $cacheKey;

    public function __construct($filepath, $cacheKey)
    {
        $this->filepath = $filepath;
        $this->department = Department::all();
        $this->cacheKey = $cacheKey;
    }

    public function handle()
    {
        try {
            DB::beginTransaction();

            $reader = new Reader();
            $reader->open(storage_path('app/public/' . $this->filepath));
            $dataIkw = [];
            $dataIkwMeeting = [];
            $dataRevisionIkw0 = [];
            $dataRevisionIkw = [];
            $dataPosition = [];
            $sheetCollections = [];
            $headers = NULL;

            foreach ($reader->getSheetIterator() as $sheet) {
                $sheetCollections[$sheet->getName()] = LazyCollection::make(function () use ($sheet) {
                    $headersYielded = false;
                    foreach ($sheet->getRowIterator() as $key => $row) {
                        if ($key == 1 && !$headersYielded) {
                            yield 'headers' => $row->toArray();
                            $headersYielded = true;
                            continue;
                        }

                        yield $row->toArray();
                    }
                });
            }


            if (isset($sheetCollections["Data IKW"])) {
                $sheetCollections["Data IKW"]->chunk(200)->each(function ($rows) use (&$dataIkw, &$dataRevisionIkw0, &$dataIkwMeeting, &$dataPosition, &$headers) {
                    $rowData = $rows->toArray();

                    if (isset($rowData['headers'])) {
                        $headers = $rowData['headers'];
                    }

                    foreach ($rows as $key => $row) {
                        if ($key === 'headers') {
                            continue;
                        }

                        $getIkwRows = $this->saveDataIkw($dataIkw, $dataRevisionIkw0, $dataIkwMeeting, $dataPosition, $row, $headers);

                        $dataIkw = $getIkwRows['dataIkw'];
                        $dataIkwMeeting = $getIkwRows['dataIkwMeeting'];
                        $dataPosition = $getIkwRows['dataIkwPosition'];
                        $dataRevisionIkw0 = $getIkwRows['dataRevisionIkw0'];
                    }


                    $this->insertChunkIkw($dataIkw, $dataIkwMeeting, $dataPosition);
                    $this->insertChunkIkwRev0($dataRevisionIkw0);

                    $dataIkw = [];
                    $dataIkwMeeting = [];
                    $dataPosition = [];
                });

                if (count($dataIkw) != 0) {
                    $this->insertChunkIkw($dataIkw, $dataIkwMeeting, $dataPosition);
                    $dataIkw = [];
                    $dataIkwMeeting = [];
                    $dataPosition = [];
                }
            }

            if (isset($sheetCollections["Data Revisi"])) {
                $sheetCollections["Data Revisi"]->chunk(200)->each(function ($rows) use (&$dataRevisionIkw) {
                    foreach ($rows as $key => $row) {
                        if ($key === 'headers') {
                            continue;
                        }

                        $dataRevisionIkw = $this->saveDataRevisionIkw($dataRevisionIkw, $row);
                    }


                    $this->insertChunkRevisionIkw($dataRevisionIkw);
                    $dataRevisionIkw = [];
                });

                if (count($dataRevisionIkw) != 0) {
                    $this->insertChunkRevisionIkw($dataRevisionIkw);
                    $dataRevisionIkw = [];
                }
            }

            if (!isset($sheetCollections["Data IKW"]) && !isset($sheetCollections["Data Revisi"])) {
                Cache::put(
                    $this->cacheKey,
                    [
                        'status' => 500,
                        'message' => 'Sheet not found please make sure you have the correct format!',
                    ],
                    now()->addMinutes(3)
                );
                Storage::delete($this->filepath);
                DB::rollBack();

                return false;
            }

            Storage::delete($this->filepath);

            DB::commit();

            Cache::put(
                $this->cacheKey,
                [
                    'status' => 201,
                    'message' => 'Successfully imported data',
                ],
                now()->addMinutes(3)
            );

            return true;
        } catch (\Exception $e) {
            Cache::put(
                $this->cacheKey,
                [
                    'status' => 500,
                    'message' => 'Failed to process: ' . $e->getMessage(),
                ],
                now()->addMinutes(3)
            );
            Storage::delete($this->filepath);

            DB::rollBack();

            return false;
        } finally {
            $reader->close();
        }
    }

    public function saveDataIkw($dataIkw, $dataRevisionIkw0, $dataIkwMeeting, $dataPosition, $row, $headers)
    {
        $departmentId = $this->department->where('code', $row[0])->first()->id ?? null;


        if (is_null($departmentId)) {
            return [
                'dataIkw'           => $dataIkw,
                'dataRevisionIkw0'  => $dataRevisionIkw0,
                'dataIkwMeeting'    => $dataIkwMeeting,
                'dataIkwPosition'   => $dataPosition,
            ];
        }

        $cleanCode = preg_replace('/^\(H\d*\)\s*/', '', $row[1]);

        $key = $departmentId . '_' . $cleanCode;

        $dataIkw[$key] = [
            'department_id'                => $this->department->where('code', $row[0])->first()->id ?? NULL,
            'code'                         => $cleanCode,
            'name'                         => $row[2],
            'total_page'                   => (int) $row[4],
            'registration_date'            => $this->parseDate($row[5]),
            'print_by_back_office_date'    => $this->parseDate($row[15]),
            'submit_to_department_date'    => $this->parseDate($row[16]),
            'ikw_return_date'              => $this->parseDate($row[17]),
            'ikw_creation_duration'        => (int) $row[28],
            'status_document'              => $row[29],
            'last_update_date'             => $this->parseDate($row[30]),

        ];

        // IF IKW REV NO EQUAL TO 0
        if ((int) $row[3] == 0) {
            $dataRevisionIkw0[] = [
                'department_code'               => $row[0],
                'ikw_code'                      => $cleanCode,
                'revision_no'                   => (int) $row[3],
                'reason'                        => "",
                'process_status'                => NULL,
                'ikw_fix_status'                => NULL,
                'confirmation'                  => NULL,
                'change_description'            => "",
                'submission_no'                 => "",
                'submission_received_date'      => NULL,
                'submission_mr_date'            => NULL,
                'backoffice_return_date'        => NULL,
                'revision_status'               => 0,
                'print_date'                    => NULL,
                'handover_date'                 => NULL,
                'signature_mr_date'             => NULL,
                'distribution_date'             => NULL,
                'document_return_date'          => NULL,
                'document_disposal_date'        => NULL,
                'document_location_description' => "",
                'revision_description'          => "",
                'status_check'                  => 0,
            ];
        }

        foreach ($row as $index => $cell) {
            // to check if cell is date time
            $isDateTime = $cell instanceof \DateTimeImmutable;

            // check if header contain Tanggal Meeting
            if (preg_match('/^Tanggal Meeting \d+$/i', $headers[$index])) {
                $meetingDuration = isset($row[$index + 1]) ? $row[$index + 1] : NULL;
                $meetingOKNOK = isset($row[$index + 2]) ? $row[$index + 2] : NULL;
                $dataIkwMeeting[] = [
                    'department_id'     => $departmentId,
                    'ikw_code'          => $cleanCode,
                    'ikw_meeting_no'    => $index,
                    'revision_no'       => (int) $row[3],
                    'meeting_date'      => $isDateTime ? $this->parseDate($cell) : NULL,
                    'meeting_duration'  => (int) $meetingDuration,
                    'revision_status'   => $meetingOKNOK,
                ];
            }

            // check if header contain Position Call
            if (preg_match('/Position Call Number\s*\d*/i', $headers[$index])) {
                $positionCall = $cell;
                $fieldOperator = isset($row[$index + 1]) ? $row[$index + 1] : NULL;
                $dataPosition[] = [
                    'department_id'         => $departmentId,
                    'ikw_code'              => $cleanCode,
                    'ikw_position_no'       => $index,
                    'revision_no'           => (int) $row[3],
                    'position_call_number'  => $positionCall,
                    'field_operator'        => $fieldOperator,
                ];
            }
        }


        return [
            'dataIkw'           => $dataIkw,
            'dataRevisionIkw0'  => $dataRevisionIkw0,
            'dataIkwMeeting'    => $dataIkwMeeting,
            'dataIkwPosition'   => $dataPosition,
        ];
    }

    public function saveDataRevisionIkw($dataIkwRevision, $row)
    {

        $ikw_id = $this->findDataIkw($row[3], $row[2])->id ?? NULL;

        if (is_null($ikw_id)) {
            return $dataIkwRevision;
        }

        $dataIkwRevision[] = [
            'ikw_id'                        => $ikw_id ?? NULL,
            'ikw_code'                      => $row[3],
            'revision_no'                   => (int) $row[4],
            'reason'                        => $row[5],
            'process_status'                => ($row[6] === "DONE") ? 1 : (($row[6] === "FOD - PENGAJUAN") ? 2 : (($row[6] === "FU-LO") ? 3 : 4)),
            'ikw_fix_status'                => ($row[7] === "MAJOR") ? 1 : (($row[7] === "MINOR") ? 2 : (($row[7] === "HAPUS") ? 3 : (($row[7] === "On Progress") ? 4 : 5))),
            'confirmation'                  => $row[8] == 'HAPUS' ? 1 : 0,
            'change_description'            => $row[9],
            'submission_no'                 => $row[10],
            'submission_received_date'      => $this->parseDate($row[11]) ?? NULL,
            'submission_mr_date'            => $this->parseDate($row[12]) ?? NULL,
            'backoffice_return_date'        => $this->parseDate($row[13]) ?? NULL,
            'revision_status'               => ($row[14] === "MAJOR") ? 1 : (($row[14] === "MINOR") ? 2 : 3),
            'print_date'                    => $this->parseDate($row[15]) ?? NULL,
            'handover_date'                 => $this->parseDate($row[16]) ?? NULL,
            'signature_mr_date'             => $this->parseDate($row[17]) ?? NULL,
            'distribution_date'             => $this->parseDate($row[18]) ?? NULL,
            'document_return_date'          => $this->parseDate($row[19]) ?? NULL,
            'document_disposal_date'        => $this->parseDate($row[20]) ?? NULL,
            'document_location_description' => $row[21],
            'revision_description'          => $row[22],
            'status_check'                  => $row[25] == 'TRUE' ? 1 : 0,
        ];


        return $dataIkwRevision;
    }

    public function insertChunkIkw($dataIkw, $dataIkwMeeting, $dataPosition)
    {
        IKW::upsert(array_values($dataIkw), ['code', 'department_id_non_null'], ['name', 'total_page', 'registration_date', 'print_by_back_office_date', 'submit_to_department_date', 'ikw_return_date', 'ikw_creation_duration', 'status_document', 'last_update_date']);

        IkwMeeting::upsert($dataIkwMeeting, ['department_id_non_null', 'ikw_code', 'ikw_meeting_no', 'revision_no'], ['ikw_revision_id', 'meeting_date', 'meeting_duration', 'revision_status']);

        IkwPosition::upsert($dataPosition, ['department_id_non_null', 'ikw_code', 'ikw_position_no', 'revision_no'], ['ikw_revision_id', 'position_call_number', 'field_operator']);
    }

    public function insertChunkIkwRev0($dataRevisionIkw0)
    {
        $insertedData = [];

        foreach ($dataRevisionIkw0 as $data) {
            $ikw = $this->findDataIkw($data['ikw_code'], $data['department_code']);
            $insertedData[] = [
                'ikw_id'                        => $ikw->id ?? null,
                'ikw_code'                      => $data['ikw_code'],
                'revision_no'                   => $data['revision_no'],
                'reason'                        => $data['reason'],
                'process_status'                => $data['process_status'],
                'ikw_fix_status'                => $data['ikw_fix_status'],
                'confirmation'                  => $data['confirmation'],
                'change_description'            => $data['change_description'],
                'submission_no'                 => $data['submission_no'],
                'submission_received_date'      => $data['submission_received_date'],
                'submission_mr_date'            => $data['submission_mr_date'],
                'backoffice_return_date'        => $data['backoffice_return_date'],
                'revision_status'               => $data['revision_status'],
                'print_date'                    => $data['print_date'],
                'handover_date'                 => $data['handover_date'],
                'signature_mr_date'             => $data['signature_mr_date'],
                'distribution_date'             => $data['distribution_date'],
                'document_return_date'          => $data['document_return_date'],
                'document_disposal_date'        => $data['document_disposal_date'],
                'document_location_description' => $data['document_location_description'],
                'revision_description'          => $data['revision_description'],
                'status_check'                  => $data['status_check'],
            ];
        }

        IKWRevision::upsert($insertedData, ['ikw_id_non_null', 'ikw_code', 'revision_no'], [
            'ikw_id',
            'reason',
            'process_status',
            'ikw_fix_status',
            'confirmation',
            'change_description',
            'submission_no',
            'submission_received_date',
            'submission_mr_date',
            'backoffice_return_date',
            'revision_status',
            'print_date',
            'handover_date',
            'signature_mr_date',
            'distribution_date',
            'document_return_date',
            'document_disposal_date',
            'document_location_description',
            'revision_description',
            'status_check',
        ]);
    }

    public function insertChunkRevisionIkw($dataRevisionIkw)
    {
        IKWRevision::upsert($dataRevisionIkw, ['ikw_id_non_null', 'ikw_code', 'revision_no'], [
            'ikw_id',
            'reason',
            'process_status',
            'ikw_fix_status',
            'confirmation',
            'change_description',
            'submission_no',
            'submission_received_date',
            'submission_mr_date',
            'backoffice_return_date',
            'revision_status',
            'print_date',
            'handover_date',
            'signature_mr_date',
            'distribution_date',
            'document_return_date',
            'document_disposal_date',
            'document_location_description',
            'revision_description',
            'status_check',
        ]);


        DB::table('ikw_meetings as im')
            ->join('ikws as i', 'im.ikw_code', '=', 'i.code')
            ->join('ikw_revisions as ir', function ($join) {
                $join->on('ir.revision_no', '=', 'im.revision_no')
                    ->on('ir.ikw_id', '=', 'i.id');
            })
            ->whereNull('im.ikw_revision_id')
            ->update([
                'im.ikw_revision_id' => DB::raw('ir.id')
            ]);


        DB::table('ikw_positions as ip')
            ->join('ikws as i', 'ip.ikw_code', '=', 'i.code')
            ->join('ikw_revisions as ir', function ($join) {
                $join->on('ir.revision_no', '=', 'ip.revision_no')
                    ->on('ir.ikw_id', '=', 'i.id');
            })
            ->whereNull('ip.ikw_revision_id')
            ->update([
                'ip.ikw_revision_id' => DB::raw('ir.id')
            ]);
    }

    public function findDataIkw($arg1, $arg2)
    {
        return IKW::where('code', $arg1)
            ->whereHas('department', function ($query) use ($arg2) {
                $query->where('code', $arg2);
            })->first();
    }

    public function parseDate($date)
    {
        $isDateTime = $date instanceof \DateTimeImmutable;

        return  $isDateTime ? CarbonImmutable::instance($date)->format('Y-m-d') : NULL;
    }
}
