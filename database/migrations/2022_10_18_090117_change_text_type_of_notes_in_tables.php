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
        Schema::table('events', function (Blueprint $table) {
            $table->mediumText('note')->nullable()->change();
        });
        Schema::table('functions', function (Blueprint $table) {
            $table->mediumText('note')->nullable()->change();
        });
        Schema::table('tasks', function (Blueprint $table) {
            $table->mediumText('note')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('note')->nullable()->change();
        });
        Schema::table('functions', function (Blueprint $table) {
            $table->string('note')->nullable()->change();
        });
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('note')->nullable()->change();
        });
    }
};
