<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePropertydataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('propertydata', function (Blueprint $table) {
            $table->bigIncrements('idPropertydata');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->foreignId('created_by')->constrained('users', 'idUser');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->foreignId('updated_by')->constrained('users', 'idUser');
            $table->string('keyPropertyData', 255);
            $table->longText('valuePropertyData');
            $table->foreignId('idProperty')->constrained('property', 'idProperty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('propertydata');
    }
}
