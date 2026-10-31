<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use PDOException;

class StockOpnameController extends Controller
{
    public function index()
    {
        try {
            $opnames = StockOpname::with([
                'product:id,name,code',
                'user:id,name',
            ])->latest('counted_at')->latest()->paginate(10);

            return view('stock-opnames.index', compact('opnames'));
        } catch (PDOException $exception) {
            return $this->databaseErrorResponse($exception, 'stock_opnames.index');
        }
    }

    public function create()
    {
        try {
            $products = Product::orderBy('name')->get(['id', 'name', 'code', 'stock']);

            return view('stock-opnames.create', compact('products'));
        } catch (PDOException $exception) {
            return $this->databaseErrorResponse($exception, 'stock_opnames.create');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->whereNull('deleted_at'),
            ],
            'counted_stock' => 'required|integer|min:0|max:2147483647',
            'counted_at' => 'required|date|before_or_equal:today',
            'note' => 'nullable|string|max:2000',
        ]);

        try {
            $opname = DB::transaction(function () use ($validated): StockOpname {
                $product = Product::whereKey($validated['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $systemStock = (int) $product->stock;
                $countedStock = (int) $validated['counted_stock'];

                $opname = StockOpname::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'system_stock' => $systemStock,
                    'counted_stock' => $countedStock,
                    'difference' => $countedStock - $systemStock,
                    'counted_at' => $validated['counted_at'],
                    'note' => $validated['note'] ?? null,
                ]);

                $product->update(['stock' => $countedStock]);

                return $opname;
            });

            return redirect()->route('stock-opnames.show', $opname)
                ->with('success', 'Stok produk berhasil disesuaikan berdasarkan hasil opname.');
        } catch (PDOException $exception) {
            return $this->databaseErrorRedirect($exception, 'stock_opnames.store');
        }
    }

    public function show(StockOpname $stockOpname)
    {
        try {
            $stockOpname->load(['product', 'user']);

            return view('stock-opnames.show', compact('stockOpname'));
        } catch (PDOException $exception) {
            return $this->databaseErrorResponse($exception, 'stock_opnames.show');
        }
    }
}
