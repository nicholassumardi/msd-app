<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIkwMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ikw_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ikw_revision_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('revision_no')->nullable();
            $table->string('ikw_code')->nullable();
            $table->integer('ikw_meeting_no')->nullable();
            $table->date('meeting_date')->nullable();
            $table->integer('meeting_duration')->nullable();
            $table->string('revision_status')->nullable()->comment('OK | NOK | REVISI | HAPUS');
            $table->integer('department_id_non_null')->virtualAs('COALESCE(department_id, 0)');
            $table->unique(['department_id_non_null', 'ikw_code', 'ikw_meeting_no', 'revision_no'], 'ikw_meeting_unique');
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
        Schema::dropIfExists('ikw_meetings');
    }
}
