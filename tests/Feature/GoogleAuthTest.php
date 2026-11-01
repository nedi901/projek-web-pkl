<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User as SocialiteUser;

uses(RefreshDatabase::class);

test('google redirect route redirects to login when google credentials are not configured', function () {
    config()->set('services.google.client_id', null);
    config()->set('services.google.client_secret', null);

    $response = $this->get(route('auth.google'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors(['login']);
});

test('google redirect route redirects to google oauth provider when configured', function () {
    config()->set('services.google.client_id', 'test-client-id');
    config()->set('services.google.client_secret', 'test-client-secret');

    $response = $this->get(route('auth.google'));

    $response->assertRedirect();
    expect($response->headers->get('Location'))->toContain('accounts.google.com');
});

function googleMockUser(string $id, string $email, string $name = 'Pengguna Google'): void
{
    $socialiteUser = Mockery::mock(SocialiteUser::class);
    $socialiteUser->shouldReceive('getId')->andReturn($id);
    $socialiteUser->shouldReceive('getEmail')->andReturn($email);
    $socialiteUser->shouldReceive('getName')->andReturn($name);
    $socialiteUser->shouldReceive('getNickname')->andReturn('nick');
    $socialiteUser->shouldReceive('getAvatar')->andReturn(null);

    $provider = Mockery::mock(GoogleProvider::class);
    $provider->shouldReceive('user')->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
}

test('google callback rejects an email that is not registered by a super user', function () {
    googleMockUser('google-uid-001', 'baru@example.com', 'Pengguna Baru');

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors(['login']);
    $this->assertGuest();
    expect(User::where('email', 'baru@example.com')->exists())->toBeFalse();
});

test('google callback creates the initial super user from SUPER_USER_EMAIL', function () {
    config()->set('services.super_user_email', 'Owner@Example.com');
    googleMockUser('google-uid-009', 'owner@example.com', 'Pemilik');

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('superuser.dashboard'));
    $this->assertAuthenticated();

    $user = User::where('email', 'owner@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('super_user');
    expect($user->google_id)->toBe('google-uid-009');
    expect($user->password)->toBeNull();
});

test('google callback does not auto-create other emails even when SUPER_USER_EMAIL is set', function () {
    config()->set('services.super_user_email', 'owner@example.com');
    googleMockUser('google-uid-010', 'orang-lain@example.com');

    $this->get(route('auth.google.callback'))->assertRedirect(route('login'));

    $this->assertGuest();
    expect(User::where('email', 'orang-lain@example.com')->exists())->toBeFalse();
});

test('google callback links google_id to existing user account with the same email', function () {
    $existingUser = User::create([
        'name' => 'Akun Lama',
        'username' => 'akunlama',
        'email' => 'lama@example.com',
        'password' => bcrypt('secret123'),
        'role' => 'admin',
        'domain_akses' => 'batubara',
        'is_active' => true,
    ]);

    $socialiteUser = Mockery::mock(SocialiteUser::class);
    $socialiteUser->shouldReceive('getId')->andReturn('google-uid-002');
    $socialiteUser->shouldReceive('getEmail')->andReturn('lama@example.com');
    $socialiteUser->shouldReceive('getName')->andReturn('Akun Lama Google');
    $socialiteUser->shouldReceive('getNickname')->andReturn('akunlama');
    $socialiteUser->shouldReceive('getAvatar')->andReturn('https://avatar.google.com/lama.jpg');

    $provider = Mockery::mock(GoogleProvider::class);
    $provider->shouldReceive('user')->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('admin.batubara.grafik.index'));
    $this->assertAuthenticatedAs($existingUser);

    $existingUser->refresh();
    expect($existingUser->google_id)->toBe('google-uid-002');
    expect($existingUser->avatar)->toBe('https://avatar.google.com/lama.jpg');
    expect($existingUser->role)->toBe('admin');
});

test('google callback blocks inactive user from logging in', function () {
    $inactiveUser = User::create([
        'name' => 'Akun Dinonaktifkan',
        'username' => 'nonaktif',
        'email' => 'nonaktif@example.com',
        'password' => bcrypt('secret123'),
        'role' => 'user',
        'is_active' => false,
    ]);

    $socialiteUser = Mockery::mock(SocialiteUser::class);
    $socialiteUser->shouldReceive('getId')->andReturn('google-uid-003');
    $socialiteUser->shouldReceive('getEmail')->andReturn('nonaktif@example.com');
    $socialiteUser->shouldReceive('getName')->andReturn('Akun Dinonaktifkan');
    $socialiteUser->shouldReceive('getNickname')->andReturn('nonaktif');
    $socialiteUser->shouldReceive('getAvatar')->andReturn(null);

    $provider = Mockery::mock(GoogleProvider::class);
    $provider->shouldReceive('user')->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors(['login']);
    $this->assertGuest();
});

test('google callback redirects with error when user cancels authorization', function () {
    $response = $this->get(route('auth.google.callback', ['error' => 'access_denied']));

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors(['login']);
    $this->assertGuest();
});
