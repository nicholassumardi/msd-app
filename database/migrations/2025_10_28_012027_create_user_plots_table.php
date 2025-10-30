<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_plots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('structure_plot_id')->nullable()->constrained()->cascadeOnDelete();
            $table->bigInteger('parent_id')->unsigned()->default(0);
            $table->string('id_staff')->nullable();
            $table->string('employee_type')->nullable();
            $table->date('assign_date')->nullable();
            $table->date('reassign_date')->nullable();
            $table->tinyInteger('status');
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
        Schema::dropIfExists('user_plots');
    }
}
