<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete()->comment('ADMIN | PURCHASING | PLANER');
            $table->string('org_level')->nullable()->comment("ST || NS || SE || SD || DV)");
            $table->string('job_family')->nullable();
            $table->string('code')->nullable();
            $table->string('full_code')->nullable();
            $table->integer('level')->nullable();
            $table->string('position')->nullable();
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
        Schema::dropIfExists('job_codes');
    }
}
