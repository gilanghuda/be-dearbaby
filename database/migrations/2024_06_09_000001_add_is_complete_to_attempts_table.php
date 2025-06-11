<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCompleteToAttemptsTable extends Migration
{
    public function up()
    {
        Schema::table('attempts', function (Blueprint $table) {
            $table->boolean('is_complete')->default(false)->after('score');
        });
    }

    public function down()
    {
        Schema::table('attempts', function (Blueprint $table) {
            $table->dropColumn('is_complete');
        });
    }
}
