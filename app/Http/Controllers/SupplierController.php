<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use PDOException;

class SupplierController extends Controller
{
    public function index()
    {
        try {
            $suppliers = Supplier::withCount('transactions')->latest()->paginate(10);

            return view('suppliers.index', compact('suppliers'));
        } catch (PDOException $exception) {
            return $this->databaseErrorResponse($exception, 'suppliers.index');
        }
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:2000',
        ]);

        try {
            $supplier = Supplier::create($validated);

            return redirect()->route('suppliers.show', $supplier)
                ->with('success', 'Pemasok berhasil ditambahkan.');
        } catch (PDOException $exception) {
            return $this->databaseErrorRedirect($exception, 'suppliers.store');
        }
    }

    public function show(Supplier $supplier)
    {
        try {
            $transactions = $supplier->transactions()
                ->with(['product', 'user'])
                ->latest()
                ->limit(10)
                ->paginate(10);

            return view('suppliers.show', compact('supplier', 'transactions'));
        } catch (PDOException $exception) {
            return $this->databaseErrorResponse($exception, 'suppliers.show');
        }
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:2000',
        ]);

        try {
            $supplier->update($validated);

            return redirect()->route('suppliers.show', $supplier)
                ->with('success', 'Data pemasok berhasil diperbarui.');
        } catch (PDOException $exception) {
            return $this->databaseErrorRedirect($exception, 'suppliers.update');
        }
    }

    public function destroy(Supplier $supplier)
    {
        try {
            $supplier->delete();

            return redirect()->route('suppliers.index')
                ->with('success', 'Pemasok berhasil dinonaktifkan.');
        } catch (PDOException $exception) {
            return $this->databaseErrorRedirect($exception, 'suppliers.destroy');
        }
    }
}
