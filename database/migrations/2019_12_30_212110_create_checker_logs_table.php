<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckerLogsTable extends Migration
{
    public function up()
    {
        Schema::create('checker_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('checker_id')->index();
            $table->string('message', '1000');
            $table->integer('level');
            $table->timestamps();
            $table->dateTime('resolved_at')->nullable()->index();
        });
    }

    public function down()
    {
        Schema::dropIfExists('checker_logs');
    }
}
