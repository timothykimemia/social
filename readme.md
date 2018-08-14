### Laravel SocialLogin Boilerplate

For developers who wish to create Laravel Socialite Controller to register or login user on their application

#### Steps

- Folder structure is of own choice.

- Create `Social.php` Model with Migration and relate with `User.php`. We'll come back to them.

```bash
    php artisan make:model Models\Link\Social -m
```

- Major fundamental is making sure you have set social services on `config\services.php` for API callbacks.

```php
    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_REDIRECT_URL'),
    ],
```
- Install [Laravel Socialite](https://github.com/laravel/socialite) which will handle API requests with your Laravel framework.

```bash
    composer require laravel/socialite
```

- While installing, go to your `.env` and configure the services definitions

```php
    GITHUB_CLIENT_ID=*********
    GITHUB_CLIENT_SECRET=****************
    GITHUB_REDIRECT_URL=http://127.0.0.1:8000/login/github/callback
```
- Replace localhost with live url version of your application. Localhost is restricted on most Social link APIs.

- To create `_CLIENT_ID` and `_CLIENT_SECRET`, refer from weblinks or search relative links to the platform: Google Search "Github Laravel Scout".

- Coming back to Model relation, we need to check if User has Social link. User has many social links.

```php
    public function social()
    {
        return $this->hasMany(Social::class);
    }
    
    public function hasSocialLinked($service)
    {
        return (bool) $this->social->where('service', $service)->count();
    }
```

###### Controller

- Create `SocialLoginController.php` under Auth directory.

```bash
    php artisan make:controller Auth\Social\SocialLoginController
```
- Laravel Socialite `Laravel\Socialite\Facades\Socialite` handles all the route callback requests between your application and social platform links.

- Checking if `hasSocialLinked($service)` from `User.php` model is applied here.

- Add routes for `Auth\Social\SocialLoginController.php` onto `routes\web.php` to handle the requests.

```php
    Route::group(['namespace' => 'Auth'], function () {
        Route::get('login/{service}', 'Social\SocialLoginController@redirect')->name('auth.redirect');
        Route::get('login/{service}/callback', 'Social\SocialLoginController@callback')->name('auth.callback');
    });
```

###### Middleware

- Create a php file under the `config` directory eg `social.php`. This will return defaults.

```php
    'social' => [
        'services' => [
            'github' => [
                'name => 'GitHub'
            ],
        ],
    ],
```
- Create `SocialLoginMiddleware.php` to handle middleware restrictions.

```bash
    php artisan make:middleware Social\SocialLoginMiddleware 
```
- This builds an array then check if request service exist from the array (the config file you created: `social.php`).

```php
    public function handle($request, Closure $next)
    {
        if (!in_array(strtolower($request->service), array_keys(config('social.social.services')))):
            return back()->with('danger', "Sorry, this Social link integration does not exist yet.");
        endif;
        
        return $next($request);
    }
```
- Set `'social'` on `Middleware\Kernel.php` under `protected $routeMiddleware = []`

```php
    'social' => \App\Http\Middleware\Social\SocialLoginMiddleware::class,
```

and you're done.

#### Contributing

Feel free to Contribute more If you working on such environments.

#### Security Vulnerabilities

If you discover a security vulnerability within Laravel and such conditions, please send an e-mail to Laravel team [taylor@laravel.com](mailto:taylor@laravel.com).

#### License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).