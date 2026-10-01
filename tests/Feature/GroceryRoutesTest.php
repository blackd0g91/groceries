<?php

namespace Tests\Feature;

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
    }

    public function test_main_lists_only_unselected_groceries(): void
    {
        Grocery::factory()->create(['name' => 'Arroz']);
        Grocery::factory()->create(['name' => 'Feijão', 'amount' => 2]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Arroz')
            ->assertDontSee('Feijão');
    }

    public function test_selected_lists_only_selected_groceries(): void
    {
        Grocery::factory()->create(['name' => 'Arroz']);
        Grocery::factory()->create(['name' => 'Feijão', 'amount' => 2]);

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

    public function test_select_sets_amount(): void
    {
        $grocery = Grocery::factory()->create();

        $this->post("/select/{$grocery->id}", ['value' => 3])->assertRedirect();

        $this->assertSame(3, $grocery->fresh()->amount);
    }

    public function test_select_rejects_out_of_range_values(): void
    {
        $grocery = Grocery::factory()->create();

        foreach ([0, 6, -1, 'abc', null] as $value) {
            $this->post("/select/{$grocery->id}", ['value' => $value])->assertSessionHasErrors('value');
        }

        $this->assertSame(0, $grocery->fresh()->amount);
    }

    public function test_select_returns_404_for_missing_grocery(): void
    {
        $this->post('/select/999', ['value' => 1])->assertNotFound();
    }

    public function test_state_changing_routes_reject_get(): void
    {
        $grocery = Grocery::factory()->create(['amount' => 2]);

        $this->get("/select/{$grocery->id}?value=1")->assertMethodNotAllowed();
        $this->get("/trash/{$grocery->id}")->assertMethodNotAllowed();
        $this->get('/trash-all')->assertMethodNotAllowed();
        $this->get('/purchase')->assertMethodNotAllowed();

        $this->assertSame(2, $grocery->fresh()->amount);
    }

    public function test_trash_resets_amount(): void
    {
        $grocery = Grocery::factory()->create(['amount' => 4]);

        $this->post("/trash/{$grocery->id}")->assertRedirect();

        $this->assertSame(0, $grocery->fresh()->amount);
    }

    public function test_trash_all_resets_every_amount(): void
    {
        Grocery::factory()->count(3)->create(['amount' => 2]);

        $this->post('/trash-all')->assertRedirect('main');

        $this->assertSame(0, Grocery::where('amount', '>', 0)->count());
    }

    public function test_purchase_records_selected_groceries_and_resets_them(): void
    {
        $selected = Grocery::factory()->create(['amount' => 3]);
        Grocery::factory()->create();

        $this->post('/purchase')->assertRedirect();

        $this->assertSame(1, Purchase::count());
        $purchase = Purchase::first();
        $this->assertSame($selected->id, $purchase->grocery_id);
        $this->assertSame(3, $purchase->amount);
        $this->assertSame(0, $selected->fresh()->amount);
    }

    public function test_add_creates_title_cased_grocery(): void
    {
        $this->post('/groceries/add', ['name' => '  molho   de tomate '])->assertRedirect('main');

        $this->assertDatabaseHas('groceries', ['name' => 'Molho De Tomate', 'amount' => 0]);
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
}
