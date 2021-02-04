<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateHasPropertyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hasProperty', function (Blueprint $table) {
            $table->bigIncrements('idHasProperty');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->foreignId('created_by')->constrained('users', 'idUser');
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->foreignId('updated_by')->constrained('users', 'idUser');
            $table->string('action', 255);
            $table->foreignId('idPropertyData')->constrained('propertyData', 'idPropertyData');
            $table->foreignId('idPropertyData')->constrained('userData', 'idUserData');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hasProperty');
    }
}
