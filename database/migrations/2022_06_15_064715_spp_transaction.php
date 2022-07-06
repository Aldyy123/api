<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SppTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spp_transaction', function (Blueprint $table) {
            $table->id();

            $table->char('study_year_id', 10)->index();
            $table->foreign('study_year_id')->references('study_year')->on('study_year')->onUpdate('cascade');
    
            $table->bigInteger('nisn_siswa')->index();
            $table->foreign('nisn_siswa')->references('nisn')->on('siswa')->onUpdate('cascade')->onDelete('cascade');
            
            $table->enum('month', ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']);
            $table->integer('price');
            $table->boolean('paid_off')->default(0);
            $table->integer('remain_payment');
            $table->integer('paid_user');
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
        Schema::dropIfExists('spp_transaction');
    }
}
