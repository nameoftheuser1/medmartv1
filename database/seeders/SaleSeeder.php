<?php

use Illuminate\Database\Seeder;
use App\Models\Sale;
use Carbon\Carbon;

class SalesTableSeeder extends Seeder
{
    public function run(): void
    {
        // Example data
        $data = [
            [
                'total_amount' => 15.00,
                'created_at' => Carbon::create('2024', '01', '01', '10', '00', '00'),
            ],
            [
                'total_amount' => 15.00,
                'created_at' => Carbon::create('2024', '02', '01', '12', '00', '00'),
            ],
        ];

        foreach ($data as $saleData) {
            Sale::create($saleData);
        }
    }
}
