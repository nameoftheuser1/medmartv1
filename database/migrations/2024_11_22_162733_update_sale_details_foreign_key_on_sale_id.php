<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSaleDetailsForeignKeyOnSaleId extends Migration
{
    public function up()
    {
        Schema::table('sale_details', function (Blueprint $table) {
            $table->dropForeign(['sale_id']);
        });

        Schema::table('sale_details', function (Blueprint $table) {
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('sale_details', function (Blueprint $table) {
            $table->dropForeign(['sale_id']);
        });

        Schema::table('sale_details', function (Blueprint $table) {
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('set null');
        });
    }
}
