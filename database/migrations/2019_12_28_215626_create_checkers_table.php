<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckersTable extends Migration
{
    public function up()
    {
        Schema::create('checkers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('checkable_type');
            $table->unsignedBigInteger('checkable_id');

            $table->string('checker');
            $table->json('arguments')->nullable();

            $table->dateTime('last_run')->nullable();
            $table->dateTime('next_run')->default('NOW()');
            $table->integer('interval')->default(30);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('checkers');
    }
}
