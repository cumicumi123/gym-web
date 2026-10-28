<?php

namespace Tests\Feature;

use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_login_and_register_pages_load(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Masuk');
        $this->get(route('register'))->assertOk()->assertSee('Daftar');
    }

    public function test_a_user_can_register_and_is_logged_in(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Pandu',
            'email' => 'pandu@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('program.show'));
        $this->assertAuthenticated();

        $user = User::where('email', 'pandu@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_registration_rejects_a_duplicate_email(): void
    {
        User::factory()->create(['email' => 'pandu@example.com']);

        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Pandu',
                'email' => 'pandu@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_a_user_can_log_in_and_log_out(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_login_rejects_the_wrong_password(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'salah-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_a_user_can_export_their_data_as_json(): void
    {
        $user = User::factory()->create();
        Program::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('akun.export'));

        $response->assertOk();
        $response->assertHeader('Content-Disposition');
        $response->assertJsonPath('akun.email', $user->email);
        $response->assertJsonCount(1, 'program');
    }

    public function test_a_user_can_delete_their_account_with_the_correct_password(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $this->actingAs($user)
            ->delete(route('akun.destroy'), ['password' => 'password123'])
            ->assertRedirect(route('home'));

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_account_deletion_requires_the_correct_password(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $this->actingAs($user)
            ->from(route('akun.index'))
            ->delete(route('akun.destroy'), ['password' => 'salah-password'])
            ->assertRedirect(route('akun.index'))
            ->assertSessionHasErrors('password');

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_the_account_page_shows_export_and_delete_controls(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('akun.index'))
            ->assertOk()
            ->assertSee('Ekspor data')
            ->assertSee('Hapus akun')
            ->assertSee('Baca kebijakan privasi');
    }

    public function test_the_privacy_page_loads(): void
    {
        $this->get(route('privasi'))
            ->assertOk()
            ->assertSee('Privasi')
            ->assertSee('Ekspor dan hapus');
    }
}
