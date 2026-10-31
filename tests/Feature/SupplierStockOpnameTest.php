<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockOpname;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierStockOpnameTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_suppliers(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->post(route('suppliers.store'), [
            'name' => 'PT Sumber Barang',
            'contact_name' => 'Budi',
            'phone' => '08123456789',
            'email' => 'budi@example.test',
            'address' => 'Jakarta',
        ])->assertRedirect();

        $supplier = Supplier::where('name', 'PT Sumber Barang')->firstOrFail();

        $this->get(route('suppliers.index'))->assertOk();
        $this->get(route('suppliers.show', $supplier))->assertOk();
        $this->get(route('suppliers.edit', $supplier))->assertOk();
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('stock-opnames.index'))
            ->assertSee('Stok Opname');

        $this->put(route('suppliers.update', $supplier), [
            'name' => 'PT Sumber Utama',
            'contact_name' => 'Budi',
            'phone' => '08123456789',
            'email' => 'budi@example.test',
            'address' => 'Bandung',
        ])->assertRedirect(route('suppliers.show', $supplier));

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'PT Sumber Utama',
        ]);

        $this->delete(route('suppliers.destroy', $supplier))
            ->assertRedirect(route('suppliers.index'));
        $this->assertSoftDeleted('suppliers', ['id' => $supplier->id]);
    }

    public function test_incoming_transaction_records_supplier_and_authenticated_user(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        $product = $this->createProduct();
        $supplier = Supplier::create(['name' => 'Pemasok A']);

        $this->actingAs($user)
            ->post(route('transactions.store'), [
                'product_id' => $product->id,
                'supplier_id' => $supplier->id,
                'type' => 'in',
                'quantity' => 4,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertRedirect(route('transactions.index'));

        $transaction = Transaction::firstOrFail();
        $this->assertSame($user->id, $transaction->user_id);
        $this->assertSame($supplier->id, $transaction->supplier_id);
        $this->assertSame($supplier->id, $transaction->supplier->id);
        $this->assertSame($user->id, $transaction->user->id);
        $this->assertSame(1, $supplier->transactions()->count());
        $this->assertSame(4, $product->fresh()->stock);
    }

    public function test_incoming_transaction_requires_an_active_supplier(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'staff']));
        $product = $this->createProduct();

        $this->from(route('transactions.create'))
            ->post(route('transactions.store'), [
                'product_id' => $product->id,
                'type' => 'in',
                'quantity' => 4,
                'transaction_date' => now()->toDateString(),
            ])
            ->assertSessionHasErrors('supplier_id');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertSame(0, $product->fresh()->stock);
    }

    public function test_admin_deleting_transaction_reverts_stock_atomically(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $product = $this->createProduct(['stock' => 14]);
        $transaction = Transaction::create([
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 4,
            'transaction_date' => now()->toDateString(),
        ]);

        $this->delete(route('transactions.destroy', $transaction))
            ->assertRedirect(route('transactions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
        $this->assertSame(10, $product->fresh()->stock);
    }

    public function test_deleting_outgoing_transaction_adds_its_quantity_back_to_stock(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $product = $this->createProduct(['stock' => 3]);
        $transaction = Transaction::create([
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 4,
            'transaction_date' => now()->toDateString(),
        ]);

        $this->delete(route('transactions.destroy', $transaction))
            ->assertRedirect(route('transactions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
        $this->assertSame(7, $product->fresh()->stock);
    }

    public function test_incoming_transaction_cannot_be_deleted_if_reversal_would_make_stock_negative(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $product = $this->createProduct(['stock' => 3]);
        $transaction = Transaction::create([
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 4,
            'transaction_date' => now()->toDateString(),
        ]);

        $this->from(route('transactions.index'))
            ->delete(route('transactions.destroy', $transaction))
            ->assertRedirect(route('transactions.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('transactions', ['id' => $transaction->id]);
        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_stock_opname_records_difference_and_updates_product_atomically(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $product = $this->createProduct(['stock' => 12]);

        $this->actingAs($user)
            ->post(route('stock-opnames.store'), [
                'product_id' => $product->id,
                'counted_stock' => 9,
                'counted_at' => now()->toDateString(),
                'note' => 'Tiga unit rusak.',
            ])
            ->assertRedirect();

        $opname = StockOpname::firstOrFail();
        $this->assertSame(12, $opname->system_stock);
        $this->assertSame(9, $opname->counted_stock);
        $this->assertSame(-3, $opname->difference);
        $this->assertSame($user->id, $opname->user_id);
        $this->assertSame($product->id, $opname->product->id);
        $this->assertSame($user->id, $opname->user->id);
        $this->assertSame(1, $product->stockOpnames()->count());
        $this->assertSame(9, $product->fresh()->stock);

        $this->get(route('stock-opnames.index'))->assertOk();
        $this->get(route('stock-opnames.show', $opname))->assertOk();
        $this->get(route('stock-opnames.create'))->assertOk();
    }

    public function test_staff_can_view_and_create_stock_opnames_but_cannot_manage_suppliers(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        $product = $this->createProduct(['stock' => 6]);
        $this->actingAs($user);

        $this->get(route('suppliers.index'))->assertForbidden();
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('stock-opnames.index'))
            ->assertSee('Stok Opname');
        $this->get(route('stock-opnames.index'))->assertOk();
        $this->get(route('stock-opnames.create'))->assertOk();

        $this->post(route('stock-opnames.store'), [
            'product_id' => $product->id,
            'counted_stock' => 5,
            'counted_at' => now()->toDateString(),
            'note' => 'Dihitung staff.',
        ])->assertRedirect();

        $opname = StockOpname::firstOrFail();
        $this->assertSame($user->id, $opname->user_id);
        $this->assertSame(5, $product->fresh()->stock);
        $this->get(route('stock-opnames.show', $opname))->assertOk();
    }

    public function test_guest_is_redirected_from_supplier_and_stock_opname_pages(): void
    {
        $this->get(route('suppliers.index'))->assertRedirect(route('login'));
        $this->get(route('stock-opnames.index'))->assertRedirect(route('login'));
    }

    private function createProduct(array $attributes = []): Product
    {
        $category = Category::create(['name' => 'Peralatan']);

        return Product::create(array_merge([
            'category_id' => $category->id,
            'code' => 'PRD-' . uniqid(),
            'name' => 'Barang Uji',
            'stock' => 0,
            'price' => 1000,
        ], $attributes));
    }
}
