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

test('google callback creates a new user when email does not exist', function () {
    $socialiteUser = Mockery::mock(SocialiteUser::class);
    $socialiteUser->shouldReceive('getId')->andReturn('google-uid-001');
    $socialiteUser->shouldReceive('getEmail')->andReturn('baru@example.com');
    $socialiteUser->shouldReceive('getName')->andReturn('Pengguna Baru');
    $socialiteUser->shouldReceive('getNickname')->andReturn('penggunabaru');
    $socialiteUser->shouldReceive('getAvatar')->andReturn('https://avatar.google.com/photo.jpg');

    $provider = Mockery::mock(GoogleProvider::class);
    $provider->shouldReceive('user')->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('user.dashboard'));
    $this->assertAuthenticated();

    $user = User::where('email', 'baru@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->google_id)->toBe('google-uid-001');
    expect($user->role)->toBe('user');
    expect($user->avatar)->toBe('https://avatar.google.com/photo.jpg');
    expect($user->is_active)->toBeTrue();
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
