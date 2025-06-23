<?php

use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\IkwController;
use App\Http\Controllers\Admin\JobFamilyController;
use App\Http\Controllers\Admin\JobTaskDescController;
use App\Http\Controllers\Admin\RkiController;
use App\Http\Controllers\Admin\StructureController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TrainingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [UserController::class, 'index']);
Route::post('store', [UserController::class, 'storeTest'])->name('store');
// Route::post('store', [UserController::class, 'importUpdatedUserExcel'])->name('store.ikw');
// Route::post('store', [UserController::class, 'importUserExcel'])->name('store.ikw');
// Route::post('store', [JobFamilyController::class, 'importJobFamilyExcel'])->name('store.jobcode');
// Route::post('store', [DepartmentController::class, 'importDepartmentExcel'])->name('store.dept');
// Route::post('store', [TrainingController::class, 'importTrainingExcel'])->name('store.training');
// Route::post('store', [StructureController::class, 'importStructureExcel'])->name('store.ikw');
// Route::post('store', [JobTaskDescController::class, 'importStructureExcel'])->name('store.ikw');
Route::post('store', [IkwController::class, 'importIkwExcel'])->name('store.ikw');
// Route::post('store', [RkiController::class, 'importRkiExcel'])->name('store.ikw');
// Route::post('store', [TrainingController::class, 'importTrainingExcel'])->name('store.ikw');
// Route::post('store', [StructureController::class, 'importUserJobCodeExcel'])->name('store.userjobcode');
Route::post('update', [UserController::class, 'updateTest'])->name('update');
Route::get('export', [UserController::class, 'exportTest'])->name('export');
