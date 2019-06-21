<h1 align="center">Laravel Portal</h1>

![Portal Header](https://boes.io/laravel-portal/portal-header-4x.png)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/robertboes/laravel-portal.svg?style=flat-square)](https://packagist.org/packages/robertboes/laravel-portal)
[![Build Status](https://img.shields.io/travis/robertboes/laravel-portal/master.svg?style=flat-square)](https://travis-ci.org/robertboes/laravel-portal)
[![Quality Score](https://img.shields.io/scrutinizer/g/robertboes/laravel-portal.svg?style=flat-square)](https://scrutinizer-ci.com/g/robertboes/laravel-portal)
[![Total Downloads](https://img.shields.io/packagist/dt/robertboes/laravel-portal.svg?style=flat-square)](https://packagist.org/packages/robertboes/laravel-portal)

Easily create single routes that serve guests and authenticated users.

## Why this pacakge?

Imagine a route/page you want to be visible by guests and users.
Normally you'll create a route, for example `Route::get('/', AppController::class)`, in this controller you'd do a check to see if the user is authenticated or not.
Your controller might look something like this:
```php
<?php

class AppController {
    public function __invoke()
    {
        if (Auth::check()) {
            return $this->dashboard();
        }
        
        return $this->guestPage();
    }
    
    public function guestPage()
    {
        $flights = \App\Flight::where('active', 1)
            ->orderBy('date', 'desc')
            ->take(10)
            ->get();
            
        return view('pages/guest/index', [
            'flights' => $flights,        
        ]);
    }
    
    protected function dashboard()
    {
        $flights = Auth::user()->flights->where('active', 1)
            ->orderBy('data', 'desc')
            ->take(5)
            ->get();
        
        return view('pages/auth/index', [
            'flights' => $flights,
        ]);
    }
}
```

This is a pretty basic example, but here you're returning a different view + data when the user is authenticated or not.
Laravel Portal was created to eliminate the auth check and decouple this from your controllers.
Your controllers (or methods) can be focussed only on the task they need to perform.
This is done by adding a middleware and swapping the intended action based on the authentication.

## Installation

You can install the package via composer:

```bash
composer require robertboes/laravel-portal
```

## Usage

### Using the config

Define your "route_actions" in the config. 
The array index maps to your "route name" and should contain at least a guest and authenticated action.
These simply map to the controller method you want to call. An example looks like this:

```php
'route_actions' => [
    'app' => [
        'guard' => 'web',
        'guest' => \App\Controllers\HomeController::class . '@__invoke',
        'auth'  => \App\Controllers\DashboardController::class . '@__invoke',
    ],
    'ajax.stats' -> [
        'guest' => 'App\Controllers\StatsController@global', 
        'auth'  => 'App\Controllers\StatsController@user',
    ],
],
```

Then you can reference to this config in your routing files.
You only pass the path (url) and the route_action, for example "ajax.stats"

``` php
Route::portal('/', 'app');
Route::group(['middleware' => ['ajax']], function () {
    Route::portal('/ajax/stats', 'ajax.stats');
});
```

The user that visits the route `your-laravel.app/` will automatically see the correct version. 
A guest user will only see the public page, a signed in user will see his/her dashboard.
This works for any route, JSON routes and also works with any guard.


## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Robert Boes](https://github.com/robertboes)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
