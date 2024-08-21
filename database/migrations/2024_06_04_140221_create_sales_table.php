<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->string('transaction_key', 7)->unique();
            $table->timestamps();
        });

        DB::statement('
            CREATE TRIGGER before_insert_sales
            BEFORE INSERT ON sales
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
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('transaction_key');
        });

        DB::statement('DROP TRIGGER IF EXISTS before_insert_sale_details');

        Schema::dropIfExists('sale_details');
    }
};
