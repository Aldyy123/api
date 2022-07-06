<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataFamilyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_family', function (Blueprint $table) {
            $table->id();
            $table->string('address', 100)->nullable();
            $table->string('kelurahan', 50)->nullable();
            $table->string('kecamatan', 50)->nullable();
            $table->string('rt', 5);
            $table->string('rw', 5);
            $table->char('phone')->nullable();
            $table->string('father', 30);
            $table->string('mother', 30);
            $table->string('dusun', 30)->nullable();

            $table->bigInteger('nisn_siswa')->index();
            $table->foreign('nisn_siswa')->references('nisn')->on('siswa')->onDelete('cascade')->onUpdate('cascade');
            
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
        Schema::dropIfExists('data_family');
    }
}
