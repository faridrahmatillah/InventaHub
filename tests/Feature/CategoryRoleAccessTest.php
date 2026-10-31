<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_access_any_category_action(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'staff']));
        $category = Category::create(['name' => 'ATK']);

        $this->get(route('categories.index'))->assertForbidden();
        $this->get(route('categories.create'))->assertForbidden();
        $this->post(route('categories.store'), ['name' => 'Elektronik'])->assertForbidden();
        $this->get(route('categories.show', $category))->assertForbidden();
        $this->get(route('categories.edit', $category))->assertForbidden();
        $this->put(route('categories.update', $category), ['name' => 'Alat Tulis'])->assertForbidden();
        $this->delete(route('categories.destroy', $category))->assertForbidden();
    }

    public function test_admin_can_access_category_pages(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $category = Category::create(['name' => 'ATK']);

        $this->get(route('categories.index'))->assertOk();
        $this->get(route('categories.create'))->assertOk();
        $this->get(route('categories.show', $category))->assertOk();
        $this->get(route('categories.edit', $category))->assertOk();

        $this->post(route('categories.store'), ['name' => 'Elektronik'])
            ->assertRedirect(route('categories.index'));
        $createdCategory = Category::where('name', 'Elektronik')->firstOrFail();
        $this->put(route('categories.update', $createdCategory), ['name' => 'Perangkat'])
            ->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', ['id' => $createdCategory->id, 'name' => 'Perangkat']);
        $this->delete(route('categories.destroy', $createdCategory))
            ->assertRedirect(route('categories.index'));
        $this->assertSoftDeleted('categories', ['id' => $createdCategory->id]);
    }

    public function test_guest_is_redirected_to_login_from_category_pages(): void
    {
        $this->get(route('categories.index'))->assertRedirect(route('login'));
    }
}
