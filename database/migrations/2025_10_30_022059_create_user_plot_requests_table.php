<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPlotRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_plot_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_plot_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('group')->nullable();
            $table->text('description')->nullable();
            $table->date('request_date')->nullable();
            $table->tinyInteger('status_slot')->default(0)->comment('0 =  Pending, 1 = Assigned');
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
        Schema::dropIfExists('user_plot_requests');
    }
}
