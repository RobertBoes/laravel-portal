<?php

namespace RobertBoes\LaravelPortal\Tests;

use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;
use RobertBoes\LaravelPortal\Exceptions\InvalidPortalConfig;
use RobertBoes\LaravelPortal\Exceptions\PortalActionNotDefined;
use RobertBoes\LaravelPortal\Http\Controllers\PortalController;
use RobertBoes\LaravelPortal\PortalFacade;
use RobertBoes\LaravelPortal\PortalServiceProvider;
use RobertBoes\LaravelPortal\Tests\Stubs\Controller;
use RobertBoes\LaravelPortal\Tests\Stubs\Models\User;

class LaravelTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'testbench']);

        $this->loadMigrationsFrom([
            '--database' => 'testbench',
            '--realpath' => realpath(__DIR__ . '/Stubs/migrations'),
        ]);

//        $this->withMiddleware([
//            'portal' => Portal::class
//        ]);

        $this->withoutExceptionHandling();

        Route::get('default-route', [PortalController::class, 'fallback']);
        Route::portal('auth-only', 'auth');
        Route::portal('guest-only', 'guest');
        Route::portal('valid-route', 'valid');
        Route::portal('non-existing', 'invalid');

        $this->afterApplicationCreated(function () {
            \DB::table('users')->insert([
                ['name' => 'Admin', 'email' => 'admin@exemple.com', 'password' => bcrypt('password')]
            ]);
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            PortalServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Portal' => PortalFacade::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('laravel-portal.route_actions.guest', [
            'guest'     => Controller::class . '@guest',
        ]);

        $app['config']->set('laravel-portal.route_actions.auth', [
            'auth'      => Controller::class . '@auth',
        ]);

        $app['config']->set('laravel-portal.route_actions.valid', [
            'guest'     => Controller::class . '@guest',
            'auth'      => Controller::class . '@auth',
        ]);

        $app['config']->set('auth.providers.users.model', User::class);
    }

    public function test_non_existing_config_should_throw_exception()
    {
        $this->expectException(InvalidPortalConfig::class);

        $response = $this->get('/non-existing');
        $response->assertStatus(500);
    }

    public function test_invalid_route_throws_portal_action_not_defined()
    {
        $this->expectException(PortalActionNotDefined::class);

        $response = $this->get('/default-route');
        $response->assertStatus(500);
    }

    public function test_auth_route_as_guest()
    {
        $this->expectException(PortalActionNotDefined::class);

        $response = $this->get('/auth-only');
        $response->assertStatus(500);
    }

    public function test_auth_route_as_auth()
    {
        $user = User::first();
        $this->actingAs($user);

        $response = $this->get('/auth-only');
        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'type' => 'auth',
        ]);
    }

    public function test_guest_route_as_guest()
    {
        $response = $this->get('/guest-only');
        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'type' => 'guest',
        ]);
    }

    public function test_guest_route_as_auth()
    {
        $user = User::first();
        $this->actingAs($user);

        $this->expectException(PortalActionNotDefined::class);

        $response = $this->get('/guest-only');
        $response->assertStatus(500);
    }

    public function test_valid_route_as_guest()
    {
        $response = $this->get('/valid-route');
        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'type' => 'guest',
        ]);
    }

    public function test_valid_route_as_auth()
    {
        $user = User::first();
        $this->actingAs($user);

        $response = $this->get('/valid-route');
        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'type' => 'auth',
        ]);
    }
}
