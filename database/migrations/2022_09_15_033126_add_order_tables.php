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
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedInteger('order')->default(1);
        });
        Schema::table('functions', function (Blueprint $table) {
            $table->unsignedInteger('order')->default(1);
        });
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedInteger('order')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('order');
        });
        Schema::table('functions', function (Blueprint $table) {
            $table->dropColumn('order');
        });
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
