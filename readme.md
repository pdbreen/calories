### Dependencies

This project has the following core dependencies:
* PHP v7.2
* sqlite
* composer
* yarn

Additional dependencies are pulled in via the composer / yarn package managers.

### Installation

Installation is consistent with most Laravel based projects.  After cloning the repository,
perform the following steps from the project root directory.

```
cp .env.example .env
composer install
yarn install
yarn dev
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan passport:install
```

### Access

Once everything is installed, the easiest way to access the site is with PHP's built in server: 

```
php artisan serve
```

By default, this will serve the site at http://localhost:8000

The database seeding process will create the following known user accounts which can be used to
login in any of the supported roles.

* **Admin** - admin@calories.com / password
* **User Manager** - user_manager@calories.com / password
* **User** - user@calories.com / password

There are a handful of additional user accounts that are randomly created by the seeding
process as well.

### Tests

The following test groups exist:
* **Unit** - exercise the Laravel Meal and User models directly
* **Feature** - exercise the REST api exposed via the controllers

The full suite of tests can be run with the following command in the project root directory:

```
./vendor/bin/phpunit
``` 

### Development Stack

The core packages used to implement this SPA are as follows:
* **Laravel 5.8** - database models, REST controllers and test scaffolding
* **Laravel Passport** - (https://laravel.com/docs/5.8/passport) token grants for user API authorization
* **Vue, Vue Router, Vuex** - front end components and SPA functionality
* **vuelidate** - (https://vuelidate.netlify.com/) front end validation
* **vue-datetime** - (https://github.com/mariomka/vue-datetime) date/time picker 
* **Tailwind CSS** - (https://tailwindcss.com/docs/what-is-tailwind/) front end CSS styling

### Backend Road Map

The `User` and `Meal` models provide access to the underlying data.  This includes some helpers to
get/set attributes as well as common scope definitions for Eloquent query building.

Resource based access control is provided by the `Policies\UserPolicy` and `Policies\MealPolicy`.  This is
supplemented in a few cases with additional checks on certain routes (meals listing, for example).

The REST API is exposed via a combination of REST based resource controllers - `Http\Controllers\UserController` 
and `Http\Controllers\MealController`.  Each in turn uses a resource wrapper - `Http\Resources\UserResource`
and `Http\Resources\MealResource` - to facilitate packaging and augmenting data for use by the front end.

In addition to standard resource routes of `index, store, show, udpdate, destroy`, there are specialized
routes to retrieve any users meals (permissions allowing) as well as the login and registration process. 

### Front End Road Map

API access is axios based with each resource having an api wrapper - `js/api/users.js` and `js/api/meals.js`.

Login, registration and access token status is managed via a Vuex store - `js/store/modules/user.js` - which
in turn makes use of browser local storage for token retention across forced page refreshes.

The Vue Router configuration is in `js/routes.js`.  Routes are decorated with meta information to facilitate
auth/access checks.  In most cases, there is a one to one mapping between a route and a corresponding
Vue component.  The exception is that create/edit of both users and meals use a common component for each action.

Vue components for item lists leverage a shared `js/mixins/PageDataMixin.js`.  Similarly, create/edit views
make use of a shared `js/mixins/EditDataMixin.js`

A handful of common form inputs were factored into shared Vue components to facilitate common layout, validation and
error message display.  These can be found in the `js/components` directory.

### NOTES

* All dates & times are stored and displayed as UTC.  For a proper application, this would need additional 
discussion to understand desired handling / requirements.
* User management is restricted so that only Admins can create/modify other Admins.  User managers can still
see Admin users, but they cannot modify them.  User managers can create/modify other User Managers (as well
as ordinary users).
* Admin UI could be enhanced to show user in context when viewing / editing other users meals