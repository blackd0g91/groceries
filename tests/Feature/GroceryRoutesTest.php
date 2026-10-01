<?php

namespace Tests\Feature;

use App\Http\Middleware\RequirePassword;
use App\Models\Grocery;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroceryRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->withSession([RequirePassword::SESSION_KEY => true]);
    }

    public function test_main_lists_only_unselected_groceries(): void
    {
        Grocery::factory()->create(['name' => 'Arroz']);
        Grocery::factory()->create(['name' => 'Feijão', 'selected' => true]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Arroz')
            ->assertDontSee('Feijão');
    }

    public function test_selected_lists_only_selected_groceries(): void
    {
        Grocery::factory()->create(['name' => 'Arroz']);
        Grocery::factory()->create(['name' => 'Feijão', 'selected' => true]);

        $this->get('/selected')
            ->assertOk()
            ->assertSee('Feijão')
            ->assertDontSee('Arroz');
    }

    public function test_grocery_names_are_escaped(): void
    {
        Grocery::factory()->create(['name' => '<script>alert(1)</script>']);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    public function test_select_marks_grocery_as_selected(): void
    {
        $grocery = Grocery::factory()->create();

        $this->post("/select/{$grocery->id}")->assertRedirect();

        $this->assertTrue($grocery->fresh()->selected);
    }

    public function test_select_returns_404_for_missing_grocery(): void
    {
        $this->post('/select/999')->assertNotFound();
    }

    public function test_state_changing_routes_reject_get(): void
    {
        $grocery = Grocery::factory()->create(['selected' => true]);

        $this->get("/select/{$grocery->id}")->assertMethodNotAllowed();
        $this->get("/trash/{$grocery->id}")->assertMethodNotAllowed();
        $this->get('/trash-all')->assertMethodNotAllowed();
        $this->get('/purchase')->assertMethodNotAllowed();

        $this->assertTrue($grocery->fresh()->selected);
    }

    public function test_trash_unselects_grocery(): void
    {
        $grocery = Grocery::factory()->create(['selected' => true]);

        $this->post("/trash/{$grocery->id}")->assertRedirect();

        $this->assertFalse($grocery->fresh()->selected);
    }

    public function test_trash_all_unselects_every_grocery(): void
    {
        Grocery::factory()->count(3)->create(['selected' => true]);

        $this->post('/trash-all')->assertRedirect('main');

        $this->assertSame(0, Grocery::where('selected', true)->count());
    }

    public function test_purchase_records_selected_groceries_and_unselects_them(): void
    {
        $selected = Grocery::factory()->create(['selected' => true]);
        Grocery::factory()->create();

        $this->post('/purchase')->assertRedirect();

        $this->assertSame(1, Purchase::count());
        $this->assertSame($selected->id, Purchase::first()->grocery_id);
        $this->assertFalse($selected->fresh()->selected);
    }

    public function test_add_creates_title_cased_grocery(): void
    {
        $this->post('/groceries/add', ['name' => '  molho   de tomate '])->assertRedirect('main');

        $this->assertDatabaseHas('groceries', ['name' => 'Molho De Tomate', 'selected' => false]);
    }

    public function test_add_does_not_duplicate(): void
    {
        Grocery::factory()->create(['name' => 'Arroz']);

        $this->post('/groceries/add', ['name' => 'arroz'])->assertRedirect('main');

        $this->assertSame(1, Grocery::where('name', 'Arroz')->count());
    }

    public function test_add_validates_name(): void
    {
        $this->post('/groceries/add', ['name' => ''])->assertSessionHasErrors('name');
        $this->post('/groceries/add', ['name' => str_repeat('a', 256)])->assertSessionHasErrors('name');

        $this->assertSame(0, Grocery::count());
    }

    public function test_edit_page_shows_grocery(): void
    {
        $grocery = Grocery::factory()->create(['name' => 'Arroz']);

        $this->get("/groceries/{$grocery->id}/edit")
            ->assertOk()
            ->assertSee('value="Arroz"', false);
    }

    public function test_rename_normalizes_name(): void
    {
        $grocery = Grocery::factory()->create(['name' => 'Leitezzzz']);

        $this->patch("/groceries/{$grocery->id}", ['name' => '  leite   em pó '])->assertRedirect('main');

        $this->assertSame('Leite Em Pó', $grocery->fresh()->name);
    }

    public function test_rename_allows_keeping_same_name(): void
    {
        $grocery = Grocery::factory()->create(['name' => 'Arroz']);

        $this->patch("/groceries/{$grocery->id}", ['name' => 'arroz'])->assertSessionHasNoErrors();

        $this->assertSame('Arroz', $grocery->fresh()->name);
    }

    public function test_rename_rejects_duplicate_and_invalid_names(): void
    {
        Grocery::factory()->create(['name' => 'Arroz']);
        $grocery = Grocery::factory()->create(['name' => 'Feijão']);

        $this->patch("/groceries/{$grocery->id}", ['name' => 'arroz'])->assertSessionHasErrors('name');
        $this->patch("/groceries/{$grocery->id}", ['name' => ''])->assertSessionHasErrors('name');
        $this->patch("/groceries/{$grocery->id}", ['name' => str_repeat('a', 256)])->assertSessionHasErrors('name');

        $this->assertSame('Feijão', $grocery->fresh()->name);
    }

    public function test_delete_removes_grocery_and_its_purchases(): void
    {
        $grocery = Grocery::factory()->create();
        Purchase::create(['grocery_id' => $grocery->id]);

        $this->delete("/groceries/{$grocery->id}")->assertRedirect('main');

        $this->assertModelMissing($grocery);
        $this->assertSame(0, Purchase::count());
    }

    public function test_edit_routes_return_404_for_missing_grocery(): void
    {
        $this->get('/groceries/999/edit')->assertNotFound();
        $this->patch('/groceries/999', ['name' => 'X'])->assertNotFound();
        $this->delete('/groceries/999')->assertNotFound();
    }
}
