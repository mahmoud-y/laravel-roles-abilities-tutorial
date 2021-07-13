[Authorization](https://laravel.com/docs/7.x/authorization) is one of laravel security features, it provides a simple way to authorize user actions, in this tutorial we'll use this feature to implement roles and abilities logic.

Content:

- [Installation](#installation)
- [Models](#models)
- [Controllers](#controllers)
- [Views](#views)
- [Commands](#commands)
- [Authorization](#authorization)
- [Conclusion](#conclusion)

# Installation

- Clone the repository
- Install composer dependancies

    ```
    composer install
    ```

- Create .env file

    ```
    cp .env.example .env
    ```

- Generate application key

    ```
    php artisan key:generate
    ```

- Set database connection environment variable
- Run migrations and seeds
    
    ```
    php artisan migrate --seed
    ```

- Following are super user default credentials
    
    email: `super@example.com`, password: `secret`

- Following are demo user defaul credentials
    
    email: `user@example.com`, password: `secret`

# Models

`Role` model will group the abilities that will be granted to related users.

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    /**
     * The abilities that belong to the role.
     */
    public function abilities()
    {
        return $this->belongsToMany('App\Ability');
    }
}

```

`Ability` model represent the actions that needs to be authorized.

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ability extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The roles that belong to the ability.
     */
    public function roles()
    {
        return $this->belongsToMany('App\Role');
    }
}

```

# Controllers

To authorize controller actions we use [authorize](https://laravel.com/docs/7.x/authorization#via-controller-helpers) helper method which accept the name of the ability needed to perform the action.

`UserController` and `RoleController` handles management of users and roles including relating users to roles and roles to abilities, the logic is simply made of crud actions and eloquent relationship manipulation.

# Views

To display only the portions of the page that users are authorized to utilize we'll use [@can and @canany](https://laravel.com/docs/7.x/authorization#via-blade-templates) blade directives.

# Commands

`SyncAbilities` contain an indexed array of strings where each element is an ability, when exceuted it will sync the abilties in the database.

```php
<?php

namespace App\Console\Commands;

use App\Ability;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncAbilities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'abilities:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync abilities';

    /**
     * The abilities.
     *
     * @var string
     */
    protected $abilities = [
        'view-any-user', 'view-user', 'create-user', 'update-user', 'delete-user',
        'view-any-role', 'view-role', 'create-role', 'update-role', 'delete-role',
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $removedAbilities = Ability::whereNotIn('name', $this->abilities)->pluck('id');
        DB::table('ability_role')->whereIn('ability_id', $removedAbilities)->delete();
        Ability::whereIn('id', $removedAbilities)->delete();
        $presentAbilities = Ability::whereIn('name', $this->abilities)->get();
        $absentAbilities = $presentAbilities->isEmpty() ? $this->abilities : array_diff($this->abilities, $presentAbilities->pluck('name')->toArray());
        if ($absentAbilities) {
            $absentAbilities = array_map(function ($ability) {
                return ['name' => $ability];
            }, $absentAbilities);
            Ability::insert($absentAbilities);
        }
    }
}
```

Whenever the abilities are modifed run the following command to sync the database.

```
php artisan abilities:sync
```

`CreateSuperUser` will create a `super` user using credentials provided in `config/auth.php` which can be set using `AUTH_SUPER_USER_EMAIL` and `AUTH_SUPER_USER_EMAIL` environment variable, `super` user surpass authorization logic hence he's granted all abilities.

```php
<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSuperUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'superuser:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Super User';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        User::where('super', true)->delete();
        User::create([
            'email' => config('auth.super_user.email'),
            'name' => 'super',
            'super' => true,
            'password' => Hash::make(config('auth.super_user.password')),
        ]);
    }
}
```

Whenever the super user need to be changed, update the correspoding environment variable and run the following command which will delete the current super user and create a new one.

```
php artisan superuser:create
```

# Authorization

The authorization take place in `AuthServiceProvider`, where we use [Gate::before](https://laravel.com/docs/7.x/authorization#intercepting-gate-checks) method to intercept gate checks then we verify if the user is super or is granted the ability through any of his roles.

```php
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;

/**
 * Register any authentication / authorization services.
 *
 * @return void
 */
public function boot()
{
    $this->registerPolicies();

    //

    Gate::before(function ($user, $ability) {
        if ($user->super) {
            return true;
        } else {
            return $user
                ->roles()
                ->whereHas('abilities', function (Builder $query) use ($ability) {
                    $query->where('name', $ability);
                })
                ->exists();
        }
    });
}
```

# Conclusion

Laravel has a lot to offer, having a general idea about what's provided help in finding the best solution, in this tutorial we've used `Authorization` and `Seeders` as the base of the roles and abilities system.