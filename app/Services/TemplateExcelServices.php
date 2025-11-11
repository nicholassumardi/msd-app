<?php


namespace App\Services;

use App\Jobs\Template\ExportTemplateCorporateJob;
use App\Jobs\Template\ExportTemplateEmployeeJob;
use App\Jobs\Template\ExportTemplateIKWJob;
use App\Jobs\Template\ExportTemplateIKWRevisionJob;
use App\Jobs\Template\ExportTemplateJobCodeJob;
use App\Jobs\Template\ExportTemplateRKIJob;
use App\Jobs\Template\ExportTemplateStructureJob;
use App\Jobs\Template\ExportTemplateTrainingJob;
use App\Services\BaseServices;


class TemplateExcelServices extends BaseServices
{
    public function exportTemplateCorporate($cachekey)
    {
        $query =  ExportTemplateCorporateJob::dispatch($cachekey);

        if ($query) {
            return true;
        }

        return false;
    }

    public function exportTemplateEmployee($cachekey)
    {
        $query =  ExportTemplateEmployeeJob::dispatch($cachekey);

        if ($query) {
            return true;
        }

        return false;
    }

    public function exportTemplateStructure($cachekey)
    {
        $query =  ExportTemplateStructureJob::dispatch($cachekey);

        if ($query) {
            return true;
        }

        return false;
    }

    public function exportTemplateTraining($cachekey)
    {
        $query =  ExportTemplateTrainingJob::dispatch($cachekey);

        if ($query) {
            return true;
        }

        return false;
    }

    public function exportTemplateIKW($cachekey)
    {
        $query =  ExportTemplateIKWJob::dispatch($cachekey);

        if ($query) {
            return true;
        }

        return false;
    }

    public function exportTemplateIKWRevision($cachekey)
    {
        $query =  ExportTemplateIKWRevisionJob::dispatch($cachekey);

        if ($query) {
            return true;
        }

        return false;
    }

    public function exportTemplateRKI($cachekey)
    {
        $query =  ExportTemplateRKIJob::dispatch($cachekey);

        if ($query) {
            return true;
        }

        return false;
    }

    public function exportTemplateJobCode($cachekey)
    {
        $query =  ExportTemplateJobCodeJob::dispatch($cachekey);

        if ($query) {
            return true;
        }

        return false;
    }
}
