<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('note')->nullable();
            $table->unsignedBigInteger('function_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('start_date');
            $table->string('end_date');
            $table->string('status')->nullable();
            $table->string('priority')->default('low');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('function_id')->references('id')->on('functions');
            $table->foreign('vendor_id')->references('id')->on('vendors');
            $table->foreign('parent_id')->references('id')->on('tasks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
