<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            $table->decimal('refunded', 10, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->string('status', 50)->default('complete');
            $table->string('transaction_key', 7)->unique()->nullable();
            $table->timestamps();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::unprepared('
                CREATE TRIGGER before_insert_sales
                BEFORE INSERT ON sales
                FOR EACH ROW
                BEGIN
                    IF NEW.transaction_key IS NULL THEN
                        SET NEW.transaction_key = UPPER(SUBSTRING(MD5(RAND()), 1, 7));
                    END IF;
                END
            ');
        } elseif (DB::getDriverName() === 'sqlite') {
            DB::unprepared('
                CREATE TRIGGER set_transaction_key_insert AFTER INSERT ON sales
                BEGIN
                    UPDATE sales SET transaction_key = substr(hex(randomblob(4)), 1, 7)
                    WHERE id = NEW.id AND transaction_key IS NULL;
                END
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::unprepared('DROP TRIGGER IF EXISTS before_insert_sales');
        } elseif (DB::getDriverName() === 'sqlite') {
            DB::unprepared('DROP TRIGGER IF EXISTS set_transaction_key_insert');
        }

        Schema::dropIfExists('sales');
    }
};
