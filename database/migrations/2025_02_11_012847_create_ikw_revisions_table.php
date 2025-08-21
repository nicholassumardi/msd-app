<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIkwRevisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ikw_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ikw_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('ikw_code')->nullable();
            $table->string('revision_no')->nullable();
            $table->text('reason')->nullable();
            $table->tinyInteger('process_status')->nullable()->comment('0:Batal Revisi | 1: DONE | 2: FOD - PENGAJUAN | 3: FU-LO | 4: ON - PROGRESS');
            $table->tinyInteger('ikw_fix_status')->nullable()->comment('0:Batal Revisi | 1: MAJOR | 2: MINOR | 3: HAPUS | 4: ON - PROGRESS');
            $table->tinyInteger('confirmation')->nullable()->comment('1: HAPUS | 0: REV');
            $table->text('change_description')->nullable();
            $table->string('submission_no')->nullable();
            $table->date('submission_received_date')->nullable();
            $table->date('submission_mr_date')->nullable();
            $table->date('backoffice_return_date')->nullable();
            $table->tinyInteger('revision_status')->nullable()->comment('1 : MAJOR | 2: MINOR | 3: HAPUS');
            $table->date('print_date')->nullable();
            $table->date('handover_date')->nullable();
            $table->date('signature_mr_date')->nullable();
            $table->date('distribution_date')->nullable();
            $table->date('document_return_date')->nullable();
            $table->date('document_disposal_date')->nullable();
            $table->text('document_location_description')->nullable();
            $table->text('revision_description')->nullable();
            $table->tinyInteger('status_check')->nullable()->comment('1 : CHECK | 0 : UNCHECK');
            $table->integer('ikw_id_non_null')->virtualAs('COALESCE(ikw_id, 0)');
            $table->unique(['ikw_id_non_null', 'ikw_code', 'revision_no']);
            $table->index(['ikw_id_non_null', 'ikw_code', 'revision_no']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ikw_revisions');
    }
}
