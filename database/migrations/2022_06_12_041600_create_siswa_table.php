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
            $table->bigInteger('nisn')->primary();
            $table->date('date_birth');
            $table->integer('start_year')->nullable();
            $table->boolean('active')->nullable()->default(0);
            $table->string('name_student', 50);
            $table->integer('end_year')->nullable();

            $table->char('study_year_id')->index();
            $table->foreign('study_year_id')->references('study_year')->on('study_year');
            
            $table->char('kelas');
            $table->integer('nipd')->unique();
            $table->string('place_born');

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
