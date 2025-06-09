<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsmHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usm_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_structure_mapping_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('revision_no')->nullable();
            $table->date('valid_date')->nullable();
            $table->date('updated_date')->nullable();
            $table->date('authorized_date')->nullable();
            $table->date('approval_date')->nullable();
            $table->date('acknowledged_date')->nullable();
            $table->date('created_date')->nullable();
            $table->date('distribution_date')->nullable();
            $table->date('withdrawal_date')->nullable();
            $table->text('logs')->nullable();
            $table->unique(['revision_no', 'user_structure_mapping_id']);
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
        Schema::dropIfExists('usm_histories');
    }
}
