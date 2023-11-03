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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('note')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('ethnicity');
            $table->string('company_name')->nullable();
            $table->string('company_id')->nullable();
            $table->string('category');
            $table->string('client_id')->nullable();
            $table->string('client_name')->nullable();
            $table->string('start_date');
            $table->string('end_date');
            $table->string('status')->nullable();
            $table->string('priority')->default('low');
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
        Schema::dropIfExists('events');
    }
};
