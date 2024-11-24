<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    protected $products;

    public function __construct(Collection $products)
    {
        $this->products = $products;
    }

    /**
     * Return a collection of products.
     */
    public function collection()
    {
        return $this->products;
    }

    /**
     * Return the headings for the Excel sheet.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Product Name',
            'Generic Name',
            'Category',
            'Description',
            'Price',
            'Barcode',
            'Created At',
            'Updated At',
        ];
    }
}
