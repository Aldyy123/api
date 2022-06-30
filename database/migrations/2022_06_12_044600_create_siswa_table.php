<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiswaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->integer('nisn')->primary();
            $table->date('date_birth');
            $table->string('parent_name', 50);
            $table->integer('start_year');
            $table->string('name_student', 50);
            $table->integer('end_year')->nullable();
            $table->char('study_year_id')->index();
            $table->foreign('study_year_id')->references('study_year')->on('study_year')->onUpdate('cascade');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('siswa');
    }
}
