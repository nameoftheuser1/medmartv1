<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $suppliers = Supplier::query()
            ->where('supplier_name', 'like', "%{$search}%")
            ->orWhere('contact_info', 'like', "%{$search}%")
            ->latest()
            ->paginate(10);
            
        return view('suppliers.index', ['suppliers' => $suppliers]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'supplier_name' => ['required', 'max:50'],
            'contact_info' => ['required'],
        ]);

        Supplier::create($validatedData);

        return redirect()->route('suppliers.index')->with('success', 'The supplier is added');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        return view('suppliers.show', ['supplier' => $supplier]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', [ 'supplier' => $supplier]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validatedData = $request->validate([
            'supplier_name' => ['required', 'max:50'],
            'contact_info' => ['required'],
        ]);

        $supplier->update($validatedData);

        return redirect()->route('suppliers.index')->with('success', 'The supplier is updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return back()->with('deleted', 'The supplier was deleted');
    }
}
