<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use PDOException;

class TransactionController extends Controller
{
    public function index()
    {
        try {
            $transactions = Transaction::with(['product', 'supplier', 'user'])
                ->latest()
                ->paginate(10);

            return view('transactions.index', compact('transactions'));
        } catch (PDOException $exception) {
            return $this->databaseErrorResponse($exception, 'transactions.index');
        }
    }

    public function create()
    {
        try {
            $products = Product::orderBy('name', 'asc')->paginate(10);
            $hasCategories = Category::exists();
            $suppliers = \App\Models\Supplier::orderBy('name')->get(['id', 'name']);

            return view('transactions.create', compact('products', 'hasCategories', 'suppliers'));
        } catch (PDOException $exception) {
            return $this->databaseErrorResponse($exception, 'transactions.create');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id'       => [
                    'required',
                    'integer',
                    Rule::exists('products', 'id')->whereNull('deleted_at'),
                ],
                'type'             => 'required|in:in,out',
                'supplier_id'      => [
                    'nullable',
                    'required_if:type,in',
                    'prohibited_unless:type,in',
                    Rule::exists('suppliers', 'id')->whereNull('deleted_at'),
                ],
                'quantity'         => 'required|integer|min:1',
                'transaction_date' => 'required|date',
                'description'      => 'nullable|string|max:2000',
            ]);

            DB::transaction(function () use ($validated): void {
                $product = Product::whereKey($validated['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    $validated['type'] === 'out' &&
                    $product->stock < $validated['quantity']
                ) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Stok tidak mencukupi! Sisa stok: ' . $product->stock,
                    ]);
                }

                Transaction::create([
                    ...$validated,
                    'user_id' => auth()->id(),
                ]);

                if ($validated['type'] === 'in') {
                    $product->stock += $validated['quantity'];
                } else {
                    $product->stock -= $validated['quantity'];
                }

                $product->save();
            });

            return redirect()
                ->route('transactions.index')
                ->with('success', 'Transaksi berhasil dan stok otomatis diperbarui!');
        } catch (PDOException $exception) {
            return $this->databaseErrorRedirect($exception, 'transactions.store');
        }
    }

    public function destroy(Transaction $transaction)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang boleh menghapus transaksi.');
        }

        try {
            $reverted = DB::transaction(function () use ($transaction): bool {
                $lockedTransaction = Transaction::whereKey($transaction->id)
                    ->lockForUpdate()
                    ->firstOrFail();
                $product = Product::withTrashed()
                    ->whereKey($lockedTransaction->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    $lockedTransaction->type === 'in' &&
                    $product->stock < $lockedTransaction->quantity
                ) {
                    return false;
                }

                if ($lockedTransaction->type === 'in') {
                    $product->stock -= $lockedTransaction->quantity;
                } else {
                    $product->stock += $lockedTransaction->quantity;
                }

                $product->save();
                $lockedTransaction->delete();

                return true;
            });

            if (! $reverted) {
                return back()->with(
                    'error',
                    'Transaksi barang masuk tidak dapat dihapus karena stok saat ini lebih kecil daripada jumlah yang harus dikoreksi.'
                );
            }

            return redirect()
                ->route('transactions.index')
                ->with('success', 'Transaksi dihapus dan stok produk berhasil dikoreksi.');
        } catch (PDOException $exception) {
            return $this->databaseErrorRedirect($exception, 'transactions.destroy');
        }
    }
}
