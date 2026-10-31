<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_edit_or_update_products(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'staff']));
        $product = $this->createProduct();

        $this->get(route('products.edit', $product))->assertForbidden();
        $this->put(route('products.update', $product), [
            'category_id' => $product->category_id,
            'code' => $product->code,
            'name' => 'Nama diubah staff',
            'stock' => 999,
            'price' => 1500,
        ])->assertForbidden();

        $this->assertSame('Barang Uji', $product->fresh()->name);
        $this->assertSame(8, $product->fresh()->stock);
    }

    public function test_admin_product_update_does_not_change_stock(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $product = $this->createProduct();

        $this->put(route('products.update', $product), [
            'category_id' => $product->category_id,
            'code' => $product->code,
            'name' => 'Nama Diperbarui',
            'stock' => 999,
            'price' => 1500,
        ])->assertRedirect(route('products.index'));

        $this->assertSame('Nama Diperbarui', $product->fresh()->name);
        $this->assertSame(8, $product->fresh()->stock);
    }

    public function test_product_store_and_update_reject_soft_deleted_categories(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $category = Category::create(['name' => 'Nonaktif']);
        $category->delete();

        $this->from(route('products.create'))
            ->post(route('products.store'), [
                'category_id' => $category->id,
                'code' => 'NEW-001',
                'name' => 'Produk Baru',
                'stock' => 2,
                'price' => 1000,
            ])
            ->assertSessionHasErrors('category_id');

        $product = $this->createProduct();

        $this->from(route('products.edit', $product))
            ->put(route('products.update', $product), [
                'category_id' => $category->id,
                'code' => $product->code,
                'name' => 'Produk Diperbarui',
                'price' => 1500,
            ])
            ->assertSessionHasErrors('category_id');

        $this->assertSame('Barang Uji', $product->fresh()->name);
        $this->assertDatabaseMissing('products', ['code' => 'NEW-001']);
    }

    private function createProduct(): Product
    {
        $category = Category::create(['name' => 'Peralatan']);

        return Product::create([
            'category_id' => $category->id,
            'code' => 'PRD-TEST-001',
            'name' => 'Barang Uji',
            'stock' => 8,
            'price' => 1000,
        ]);
    }
}
