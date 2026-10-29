<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        // Ambil data transaksi beserta nama produk dari yang terbaru
        $transactions = Transaction::with('product')->latest()->get();
        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $products = Product::all();
        return view('transactions.create', compact('products'));
    }

    public function store(Request $request)
    {
        // Validasi input, hasilnya disimpan di $validated agar hanya data bersih yang masuk database
        $validated = $request->validate([
            'product_id'       => 'required|exists:products,id',
            'type'             => 'required|in:in,out',
            'quantity'         => 'required|integer|min:1',
            'transaction_date' => 'required|date',
            'description'      => 'nullable|string'
        ]);

        // Gunakan DB Transaction untuk keamanan integritas data (Syarat CPMK2 & CPMK4)
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($validated['product_id']);

            // Cek jika barang keluar, stok tidak boleh minus
            if ($validated['type'] == 'out' && $product->stock < $validated['quantity']) {
                DB::rollBack();
                return back()->withErrors(['quantity' => 'Stok tidak mencukupi! Sisa stok: ' . $product->stock])->withInput();
            }

            // 1. Simpan data transaksi (hanya field hasil validasi)
            Transaction::create($validated);

            // 2. Update stok barang
            if ($validated['type'] == 'in') {
                $product->stock += $validated['quantity'];
            } else {
                $product->stock -= $validated['quantity'];
            }
            $product->save();

            // Jika sukses semua, commit/permanenkan ke database
            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil & Stok otomatis terupdate!');

        } catch (\Exception $e) {
            // Jika ada error/gagal di tengah jalan, batalkan semua proses
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
