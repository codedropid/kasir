<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_404_error_page_renders_properly(): void
    {
        $user = User::create([
            'name' => 'Kasir Test',
            'email' => 'kasir@kafe.com',
            'password' => bcrypt('password'),
            'role' => 'kasir',
        ]);

        $response = $this->actingAs($user)->get('/halaman-yang-tidak-ada-12345');
        $response->assertStatus(404);
        $response->assertSee('Cangkir Ini Kosong');
        $response->assertSee('ERROR 404');
    }

    public function test_error_views_can_be_rendered(): void
    {
        $view404 = view('errors.404')->render();
        $this->assertStringContainsString('404', $view404);

        $view403 = view('errors.403')->render();
        $this->assertStringContainsString('403', $view403);

        $view419 = view('errors.419')->render();
        $this->assertStringContainsString('419', $view419);

        $view500 = view('errors.500')->render();
        $this->assertStringContainsString('500', $view500);

        $view503 = view('errors.503')->render();
        $this->assertStringContainsString('503', $view503);

        $view429 = view('errors.429')->render();
        $this->assertStringContainsString('429', $view429);
    }
}
