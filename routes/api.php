<?php

use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\ClassificationController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EvaluationController;
use App\Http\Controllers\Admin\IkwController;
use App\Http\Controllers\Admin\JobFamilyController;
use App\Http\Controllers\Admin\RkiController;
use App\Http\Controllers\Admin\StructureController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TrainingController;
use App\Http\Controllers\TemplateExcelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::prefix('admin')->namespace('Admin')->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('/{id}', [DashboardController::class, 'index']);
    });

    Route::prefix('master_data')->group(function () {
        Route::prefix('certificate')->group(function () {
            Route::get('/', [CertificateController::class, 'index']);
            Route::post('import', [CertificateController::class, 'importCertificateExcel']);
            Route::post('store', [CertificateController::class, 'store']);
            Route::put('update/{id}', [CertificateController::class, 'update']);
            Route::get('show/{id}', [CertificateController::class, 'show']);
            Route::get('show', [CertificateController::class, 'showAll']);
            Route::delete('delete/{id}', [CertificateController::class, 'destroy']);
        });

        Route::prefix('company')->group(function () {
            Route::get('/', [CompanyController::class, 'index']);
            Route::post('import', [CompanyController::class, 'importCompanyExcel']);
            Route::post('store', [CompanyController::class, 'store']);
            Route::put('update/{id}', [CompanyController::class, 'update']);
            Route::get('show/{id}', [CompanyController::class, 'show']);
            Route::get('show', [CompanyController::class, 'showAll']);
            Route::delete('delete/{id}', [CompanyController::class, 'destroy']);
        });

        Route::prefix('department')->group(function () {
            Route::get('/', [DepartmentController::class, 'index']);
            Route::post('import', [DepartmentController::class, 'importDepartmentExcel']);
            Route::post('store', [DepartmentController::class, 'store']);
            Route::put('update/{id}', [DepartmentController::class, 'update']);
            Route::get('show/{id}', [DepartmentController::class, 'show']);
            Route::get('show', [DepartmentController::class, 'showAll']);
            Route::get('show_by_company/{id}', [DepartmentController::class, 'showDepartmentByCompany']);
            Route::get('show_department_pagination', [DepartmentController::class, 'showDepartmentPagination']);
            Route::get('show_parent', [DepartmentController::class, 'showParentDepartment']);
            Route::delete('delete/{id}', [DepartmentController::class, 'destroy']);
        });

        Route::prefix('job_family')->group(function () {
            Route::prefix('category')->group(function () {
                Route::get('/', [JobFamilyController::class, 'index']);
                Route::post('import', [JobFamilyController::class, 'importJobFamilyExcel']);
                Route::post('store', [JobFamilyController::class, 'storeCategory']);
                Route::put('update/{id}', [JobFamilyController::class, 'updateCategory']);
                Route::get('show/{id}', [JobFamilyController::class, 'showCategory']);
                Route::get('show', [JobFamilyController::class, 'showCategoryAll']);
                Route::get('show_category_pagination', [JobFamilyController::class, 'showCategoryPagination']);
                Route::delete('delete/{id}', [JobFamilyController::class, 'destroyCategory']);
            });
            Route::prefix('peh_code')->group(function () {
                Route::get('/', [JobFamilyController::class, 'index']);
                Route::post('store', [JobFamilyController::class, 'storeJobCode']);
                Route::put('update/{id}', [JobFamilyController::class, 'updateJobCode']);
                Route::get('show/{id}', [JobFamilyController::class, 'showJobCode']);
                Route::get('show', [JobFamilyController::class, 'showJobCodeAll']);
                Route::get('show_job_code_pagination', [JobFamilyController::class, 'showJobCodePagination']);
                Route::delete('delete/{id}', [JobFamilyController::class, 'destroyJobCode']);
            });
            Route::prefix('job_description')->group(function () {
                Route::get('/', [JobFamilyController::class, 'index']);
                Route::post('store', [JobFamilyController::class, 'storeJobDescription']);
                Route::post('assign_job', [JobFamilyController::class, 'assignJobDescTask']);
                Route::put('update/{id}', [JobFamilyController::class, 'updateJobDescription']);
                Route::get('show/{id}', [JobFamilyController::class, 'showJobDescription']);
                Route::get('show', [JobFamilyController::class, 'showJobDescriptionAll']);
                Route::get('show_job_desc_pagination', [JobFamilyController::class, 'showJobDescriptionPagination']);
                Route::delete('delete/{id}', [JobFamilyController::class, 'destroyJobDescription']);
            });
            Route::prefix('job_task')->group(function () {
                Route::get('/', [JobFamilyController::class, 'indexJobTask']);
                Route::post('store', [JobFamilyController::class, 'storeJobTask']);
                Route::put('update/{id}', [JobFamilyController::class, 'updateJobTask']);
                Route::get('show/{id}', [JobFamilyController::class, 'showJobTask']);
                Route::get('show', [JobFamilyController::class, 'showJobTaskAll']);
                Route::delete('delete/{id}', [JobFamilyController::class, 'destroyJobTask']);
            });
            Route::prefix('ikws')->group(function () {
                Route::get('/', [IkwController::class, 'index']);
                Route::post('import', [IkwController::class, 'importIKWExcel']);
                Route::post('import_job_task_desc', [IkwController::class, 'importJobTaskDescExcel']);
                Route::post('store', [IkwController::class, 'store']);
                Route::put('update/{id}', [IkwController::class, 'update']);
                Route::get('show/{id}', [IkwController::class, 'show']);
                Route::get('show', [IkwController::class, 'showAll']);
                Route::get('show_revision', [IkwController::class, 'showAllRevision']);
                Route::get('show_revision_by_ikw/{id}', [IkwController::class, 'showIKWRevisionByIKW']);
                Route::get('show_ikw_pagination', [IkwController::class, 'showIkwPagination']);
                Route::delete('delete/{id}', [IkwController::class, 'destroyIkws']);
            });
        });

        Route::prefix('classification')->group(function () {
            Route::get('/', [ClassificationController::class, 'index']);
            Route::post('store_age', [ClassificationController::class, 'storeAge']);
            Route::post('store_working_duration', [ClassificationController::class, 'storeWorkingDuration']);
            Route::post('store_general', [ClassificationController::class, 'storeGeneral']);
            Route::put('update_age/{id}', [ClassificationController::class, 'updateAge']);
            Route::put('update_working_duration/{id}', [ClassificationController::class, 'updateWorkingDuration']);
            Route::put('update_general/{id}', [ClassificationController::class, 'updateGeneral']);
            Route::get('show_age/{id}', [ClassificationController::class, 'showAge']);
            Route::get('show_working_duration/{id}', [ClassificationController::class, 'showWorkingDuration']);
            Route::get('show_general/{id}', [ClassificationController::class, 'showGeneral']);
            Route::get('show_age', [ClassificationController::class, 'showAgeAll']);
            Route::get('show_working_duration', [ClassificationController::class, 'showWorkingDurationAll']);
            Route::get('show_general', [ClassificationController::class, 'showGeneralAll']);
            Route::delete('delete_age/{id}', [ClassificationController::class, 'destroyAge']);
            Route::delete('delete_working_duration/{id}', [ClassificationController::class, 'destroyWorkingDuration']);
            Route::delete('delete_general/{id}', [ClassificationController::class, 'destroyGeneral']);
        });
    });

    Route::prefix('employee')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('import', [UserController::class, 'importUserExcel']);
        Route::post('import_update', [UserController::class, 'importUpdateUserExcel']);
        Route::get('export', [UserController::class, 'exportDataUserExcel']);
        Route::post('store', [UserController::class, 'store']);
        Route::post('store_employee_number', [UserController::class, 'dedicatedStoreEmployeeNumber']);
        Route::put('update/{uuid}', [UserController::class, 'update']);
        Route::put('update_employee_number/{uuid}', [UserController::class, 'dedicatedUpdateEmployeeNumber']);
        Route::put('update_status/{id}', [UserController::class, 'updateStatus']);
        Route::get('show/{uuid}', [UserController::class, 'show']);
        Route::get('show', [UserController::class, 'showAll']);
        Route::get('show_user_pagination', [UserController::class, 'showUserPagination']);
        Route::get('show_user_history_pagination', [UserController::class, 'showUserHistoryPagination']);
        Route::get('show_by_department/{department_id}', [UserController::class, 'showByDepartment']);
        Route::delete('delete/{uuid}', [UserController::class, 'destroy']);
    });

    Route::prefix('structure')->group(function () {
        Route::get('/', [StructureController::class, 'index']);
        Route::post('import', [StructureController::class, 'importStructureExcel']);
        Route::post('import_user_plot', [StructureController::class, 'ImportUserPlotExcel']);
        Route::post('store_structure', [StructureController::class, 'storeStructure']);
        Route::post('store_user_plot', [StructureController::class, 'storeUserPlot']);
        Route::post('store_user_plot_request', [StructureController::class, 'storeUserPlotRequest']);
        Route::put('update_structure/{id}', [StructureController::class, 'updateStructure']);
        Route::put('update_bulk_structure', [StructureController::class, 'updateBulkUserStructure']);
        Route::put('update_user_plot/{uuid}', [StructureController::class, 'updateUserPlot']);
        Route::put('update_user_plot_request/{id}', [StructureController::class, 'updateUserPlotRequest']);
        Route::put('update_status_user_plot/{id}', [StructureController::class, 'updateStatusUserPlot']);
        Route::put('move_user_plot_request/{id}', [StructureController::class, 'moveUserPlotRequest']);
        Route::put('move_user_plot/{id}', [StructureController::class, 'moveUserPlot']);
        Route::get('show_structure/{id}', [StructureController::class, 'showStructure']);
        Route::get('show_structure', [StructureController::class, 'showAllStructure']);
        Route::get('show_structure_plot/{id}', [StructureController::class, 'showStructurePlot']);
        Route::get('show_structure_plot', [StructureController::class, 'showAllStructurePlot']);
        Route::get('show_structure_plot_pagination', [StructureController::class, 'showStructurePlotPagination']);
        Route::get('structure_by_department', [StructureController::class, 'showAllStructureByDepartment']);
        Route::get('show_user_plot/{uuid}', [StructureController::class, 'showUserPlot']);
        Route::get('show_user_plot', [StructureController::class, 'showAllUserPlot']);
        Route::get('show_user_plot_position', [StructureController::class, 'showUserPlotPosition']);
        Route::get('show_user_plot_pagination', [StructureController::class, 'showUserPlotPagination']);
        Route::get('show_structure_history_pagination', [StructureController::class, 'showStructureHistoriesPagination']);
        Route::get('show_user_by_plot/{id}', [StructureController::class, 'showUserPlotByStructurePlot']);
        Route::get('show_all_structure_hierarchy/{id}', [StructureController::class, 'showAllStructureHierarchy']);
        Route::get('show_structure_hierarchy/{id}', [StructureController::class, 'showStructureHierarchyUser']);
        Route::get('show_structure_hierarchy_parent/{id}', [StructureController::class, 'showStructureHierarchyParent']);
        Route::get('show_structure_hierarchy_children/{id}', [StructureController::class, 'showStructureHierarchyChildren']);
        Route::delete('delete_structure/{id}', [StructureController::class, 'destroyStructure']);
        Route::delete('delete_user_plot/{id}', [StructureController::class, 'destroyUserPlot']);
    });

    Route::prefix('rki')->group(function () {
        Route::get('/', [RkiController::class, 'index']);
        Route::post('import', [RkiController::class, 'importRKIExcel']);
        Route::post('store', [RkiController::class, 'store']);
        Route::get('show/{id}', [RkiController::class, 'show']);
        Route::get('show', [RkiController::class, 'showAll']);
        Route::get('show_by_structure/{id}', [RkiController::class, 'showByStructure']);
        Route::get('show_by_ikw', [RkiController::class, 'showByIKW']);
        Route::get('show_rki_pagination', [RkiController::class, 'showRkiPagination']);
        Route::put('update/{id}', [RkiController::class, 'update']);
        Route::delete('delete/{id}', [RkiController::class, 'destroy']);
    });

    Route::prefix('training')->group(function () {
        Route::get('/', [TrainingController::class, 'index']);
        Route::post('import', [TrainingController::class, 'importTrainingExcel']);
        Route::post('store', [TrainingController::class, 'store']);
        Route::get('show/{id}', [TrainingController::class, 'show']);
        Route::get('show_by_uuid/{uuid}', [TrainingController::class, 'showByUUID']);
        Route::get('show', [TrainingController::class, 'showAll']);
        Route::get('show_training_pagination', [TrainingController::class, 'showTrainingPagination']);
        Route::put('update/{id}', [TrainingController::class, 'update']);
        Route::put('update_status/{id}', [TrainingController::class, 'updateStatus']);
        Route::put('update_status_active/{id}', [TrainingController::class, 'updateStatusActive']);
        Route::delete('delete/{id}', [TrainingController::class, 'destroy']);
    });

    Route::prefix('evaluation')->group(function () {
        Route::get('/', [EvaluationController::class, 'index']);
        Route::get('show/{id}', [EvaluationController::class, 'show']);
        Route::get('show_evaluation_pagination', [EvaluationController::class, 'showEvaluationPagination']);
        Route::get('show_training_general/{id}', [EvaluationController::class, 'showTrainingPlanning']);
        Route::get('show_training_rki/{id}', [EvaluationController::class, 'showTrainingPlanningRKI']);
        Route::get('show_detail_rki/{id}', [EvaluationController::class, 'showDetailRKI']);
        Route::get('show_data_visualization', [EvaluationController::class, 'showDataVisualization']);
        Route::get('show_ikw_to_train', [EvaluationController::class, 'showIKWToTrainForTrainee']);
        Route::get('show_eligible_ikw', [EvaluationController::class, 'showEligibleIKWByTrainer']);
        Route::get('show_eligible_employee', [EvaluationController::class, 'showEligibleEmployeeByIKW']);
        Route::get('show_trainee_by_ikw', [EvaluationController::class, 'showTraineeByTrainerIKW']);
        Route::get('show_employee_training_history', [EvaluationController::class, 'showEmployeeTrainingHistory']);
        Route::get('show_subordinate', [EvaluationController::class, 'showTrainerSubordinate']);
    });

    Route::prefix('calendar')->group(function () {
        Route::get('/', [CalendarController::class, 'index']);
        Route::post('store', [CalendarController::class, 'store']);
        Route::put('update/{id}', [CalendarController::class, 'update']);
        Route::get('show/{id}', [CalendarController::class, 'show']);
        Route::get('show', [CalendarController::class, 'showAll']);
        Route::get('show_weekly', [CalendarController::class, 'showWeekly']);
        Route::delete('delete/{id}', [CalendarController::class, 'destroy']);
    });

    Route::prefix('template')->group(function () {
        Route::get('structure', [TemplateExcelController::class, 'exportTemplateStructure']);
        Route::get('employee', [TemplateExcelController::class, 'exportTemplateEmployee']);
        Route::get('rki', [TemplateExcelController::class, 'exportTemplateRKI']);
        Route::get('ikw', [TemplateExcelController::class, 'exportTemplateIKW']);
        Route::get('ikw_revision', [TemplateExcelController::class, 'exportTemplateIKWRevision']);
        Route::get('training', [TemplateExcelController::class, 'exportTemplateTraining']);
        Route::get('corporate', [TemplateExcelController::class, 'exportTemplateCorporate']);
        Route::get('job_code', [TemplateExcelController::class, 'exportTemplateJobCode']);
    });
});
