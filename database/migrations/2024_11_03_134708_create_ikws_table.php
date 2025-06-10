<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIkwsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ikws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->integer('total_page')->nullable();
            $table->date('registration_date')->nullable();
            $table->date('print_by_back_office_date')->nullable();
            $table->date('submit_to_department_date')->nullable();
            $table->date('ikw_return_date')->nullable();
            $table->integer('ikw_creation_duration')->nullable();
            $table->string('status_document')->nullable()->comment('DRAFT IKW CLOSE | IKW FINISH | IKW TERDAFTAR | ON PROGRESS');
            $table->date('last_update_date')->nullable();
            $table->string('description')->nullable();
            $table->integer('department_id_non_null')->virtualAs('COALESCE(department_id, 0)');
            $table->unique(['department_id_non_null', 'name', 'code']);
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
        Schema::dropIfExists('ikws');
    }
}
