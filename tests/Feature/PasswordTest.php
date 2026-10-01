<?php

namespace Tests\Feature;

use App\Http\Middleware\RequirePassword;
use App\Models\Grocery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        config(['app.password' => 'secret']);
    }

    public function test_pages_redirect_to_login_without_password(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/main')->assertRedirect('/login');
        $this->get('/selected')->assertRedirect('/login');
    }

    public function test_actions_are_blocked_without_password(): void
    {
        $grocery = Grocery::factory()->create();

        $this->post("/select/{$grocery->id}")->assertRedirect('/login');
        $this->post('/groceries/add', ['name' => 'Bot'])->assertRedirect('/login');
        $this->patch("/groceries/{$grocery->id}", ['name' => 'Bot'])->assertRedirect('/login');
        $this->delete("/groceries/{$grocery->id}")->assertRedirect('/login');

        $this->assertFalse($grocery->fresh()->selected);
        $this->assertSame(1, Grocery::count());
    }

    public function test_login_page_is_reachable(): void
    {
        $this->get('/login')->assertOk()->assertSee('Password');
    }

    public function test_correct_password_unlocks_site_and_returns_to_intended_page(): void
    {
        $this->get('/selected');

        $this->post('/login', ['password' => 'secret'])
            ->assertRedirect('/selected')
            ->assertSessionHas(RequirePassword::SESSION_KEY, true);

        $this->get('/')->assertOk();
    }

    public function test_wrong_password_is_rejected(): void
    {
        $this->post('/login', ['password' => 'nope'])
            ->assertSessionHasErrors('password')
            ->assertSessionMissing(RequirePassword::SESSION_KEY);
    }

    public function test_empty_configured_password_locks_everyone_out(): void
    {
        config(['app.password' => '']);

        $this->post('/login', ['password' => ''])->assertSessionHasErrors('password');
        $this->post('/login', ['password' => 'anything'])->assertSessionMissing(RequirePassword::SESSION_KEY);
    }

    public function test_login_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['password' => 'nope']);
        }

        $this->post('/login', ['password' => 'secret'])->assertStatus(429);
    }

    public function test_logout_locks_site_again(): void
    {
        $this->withSession([RequirePassword::SESSION_KEY => true])
            ->post('/logout')
            ->assertRedirect('login');

        $this->get('/')->assertRedirect('/login');
    }
}
