<?php

namespace App\Jobs;

use App\Models\Department;
use App\Models\IKW;
use App\Models\IkwMeeting;
use App\Models\IkwPosition;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;

class TestJobJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private array $dataIkwMeeting = [];
    private array $dataPosition = [];
    private $headers;
    private $rows;

    public function __construct($dataIkwMeeting, $dataPosition, $headers, $rows)
    {
        $this->dataIkwMeeting = $dataIkwMeeting;
        $this->dataPosition = $dataPosition;
        $this->headers = $headers;
        $this->rows = $rows;
    }

    public function handle()
    {
        // Pre-process headers first
        [$meetingDateIndexes, $positionCallIndexes] = $this->preprocessHeaders();

        // Use lazy collection for memory efficiency
        LazyCollection::make(function () {
            foreach ($this->rows as $row) {
                yield $row;
            }
        })->chunk(100)->each(function ($chunk) use ($meetingDateIndexes, $positionCallIndexes) {

            // Process in transactional chunks
            DB::transaction(function () use ($chunk, $meetingDateIndexes, $positionCallIndexes) {
                $dataIkw = [];
                $compositeKeys = [];

                // Batch department lookups per chunk
                $departmentIds = $this->resolveDepartmentIds($chunk);

                foreach ($chunk as $row) {
                    $cells = $row->getCells();
                    $deptId = $departmentIds[$cells[0]->getValue()] ?? null;

                    if (!$deptId) {
                        $this->logMissingDepartment($cells[0]->getValue());
                        continue;
                    }

                    // Process IKW data
                    $dataIkw[] = $this->processIkwRow($cells, $deptId);

                    // Track composite keys for cleanup
                    $compositeKeys = array_merge(
                        $compositeKeys,
                        $this->processMeetingDates($cells, $deptId, $meetingDateIndexes),
                        $this->processPositions($cells, $deptId, $positionCallIndexes)
                    );
                }

                // Batch update IKW records
                if (!empty($dataIkw)) {
                    IKW::upsert($dataIkw, ['code', 'department_id'], $this->getIkwUpdateFields());
                }

                // Cleanup previous records
                $this->cleanupPreviousRecords($compositeKeys);

                // Batch insert new records
                $this->batchInsertRelations();
            });
        });
    }

    // Helper methods below
    private function preprocessHeaders()
    {
        $meetingDates = [];
        $positionCalls = [];

        foreach ($this->headers as $index => $header) {
            if (preg_match('/^Tanggal Meeting \d+$/i', $header)) {
                $meetingDates[] = $index;
            }
            if (preg_match('/Position Call Number\s*\d*/i', $header)) {
                $positionCalls[] = $index;
            }
        }

        return [$meetingDates, $positionCalls];
    }

    private function resolveDepartmentIds($chunk)
    {
        $codes = [];
        foreach ($chunk as $row) {
            $codes[] = $row->getCells()[0]->getValue();
        }

        return Department::whereIn('code', array_unique($codes))
            ->pluck('id', 'code')
            ->toArray();
    }

    private function processIkwRow($cells, $deptId)
    {
        return [
            'department_id'                => $deptId,
            'code'                         => $cells[1]->getValue(),
            'name'                         => $cells[2]->getValue(),
            'total_page'                   => $cells[4]->getValue(),
            'registration_date'            => $this->parseDate($cells[5]),
            'print_by_back_office_date'    => $this->parseDate($cells[15]),
            'submit_to_department_date'    => $this->parseDate($cells[16]),
            'ikw_return_date'              => $this->parseDate($cells[17]),
            'ikw_creation_duration'        => $cells[28]->getValue(),
            'status_document'              => $cells[29]->getValue(),
            'last_update_date'             => $this->parseDate($cells[30]),
        ];
    }

    private function processMeetingDates($cells, $deptId, $indexes)
    {
        $keys = [];
        foreach ($indexes as $index) {
            $this->dataIkwMeeting[] = [
                'department_id'     => $deptId,
                'ikw_code'          => $cells[1]->getValue(),
                'no_revision'       => $cells[3]->getValue(),
                'meeting_date'      => $this->parseDate($cells[$index]),
                'meeting_duration'  => $cells[$index + 1]->getValue(),
                'revision_status'   => $cells[$index + 2]->getValue(),
            ];
            $keys[] = [
                'd' => $deptId,
                'n' => $cells[3]->getValue(),
                'c' => $cells[1]->getValue()
            ];
        }
        return $keys;
    }

    private function processPositions($cells, $deptId, $indexes)
    {
        $keys = [];
        foreach ($indexes as $index) {
            $this->dataPosition[] = [
                'department_id'         => $deptId,
                'ikw_code'              => $cells[1]->getValue(),
                'no_revision'           => $cells[3]->getValue(),
                'position_call_number'  => $cells[$index]->getValue(),
                'field_operator'        => $cells[$index + 1]->getValue(),
            ];
            $keys[] = [
                'd' => $deptId,
                'n' => $cells[3]->getValue(),
                'c' => $cells[1]->getValue()
            ];
        }
        return $keys;
    }

    private function cleanupPreviousRecords($keys)
    {
        $uniqueKeys = array_unique($keys, SORT_REGULAR);

        foreach (array_chunk($uniqueKeys, 500) as $chunk) {
            // Use whereRaw with JSON for better performance
            $jsonConditions = collect($chunk)->map(
                fn($k) =>
                "(department_id = {$k['d']} AND no_revision = '{$k['n']}' AND ikw_code = '{$k['c']}')"
            )->implode(' OR ');

            IkwMeeting::whereRaw($jsonConditions)->delete();
            IkwPosition::whereRaw($jsonConditions)->delete();
        }
    }

    private function batchInsertRelations()
    {
        foreach (array_chunk($this->dataIkwMeeting, 1000) as $chunk) {
            IkwMeeting::insert($chunk);
        }

        foreach (array_chunk($this->dataPosition, 1000) as $chunk) {
            IkwPosition::insert($chunk);
        }

        // Reset temporary storage
        $this->dataIkwMeeting = [];
        $this->dataPosition = [];
    }

    private function parseDate($cell)
    {
        $value = $cell->getValue();
        return $value instanceof \DateTimeImmutable
            ? CarbonImmutable::instance($value)->format('Y-m-d')
            : null;
    }

    private function getIkwUpdateFields()
    {
        return [
            'name',
            'total_page',
            'registration_date',
            'print_by_back_office_date',
            'submit_to_department_date',
            'ikw_return_date',
            'ikw_creation_duration',
            'status_document',
            'last_update_date'
        ];
    }

    private function logMissingDepartment(string $code)
    {
        logger()->error("Missing department for code: {$code}");
        // You could also increment a counter here for final report
    }
}
