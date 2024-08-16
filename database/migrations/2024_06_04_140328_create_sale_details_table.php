<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('set null');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('discount', 10, 2)->nullable();
            $table->string('transaction_key', 7)->unique();
            $table->timestamps();
        });

        DB::statement('
            CREATE TRIGGER before_insert_sale_details
            BEFORE INSERT ON sale_details
            FOR EACH ROW
            BEGIN
                SET NEW.transaction_key = (SELECT UPPER(SUBSTRING(MD5(RAND()), 1, 7)));
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_details', function (Blueprint $table) {
            $table->dropColumn('transaction_key');
        });

        DB::statement('DROP TRIGGER IF EXISTS before_insert_sale_details');

        Schema::dropIfExists('sale_details');
    }
};
