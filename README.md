# Porter

Porter is a lightweight, framework-agnostic authorisation solution for PHP applications, loosely based on [CanCan](https://github.com/ryanb/cancan). It requires PHP5.3, but other than that makes no assumptions about your framework or database structure.

## Quickstart (Laravel)

After [installing](#installation), inside your `User` model:

```php
use JR\Porter\User as Porter;

class User extends Eloquent
{
    use Porter;

    public function __construct()
    {
        parent::__construct();

        UserAbilities::setup($this);
    }
}
```

...and inside a separate `UserAbilities` class:

```php
class UserAbilities
{
    public static function setup(User $user)
    {
        $user->may('read', 'Book', function($b)
        {
            return ($b->private) ? ( $b->author_id == $this->id ) : TRUE;
        });

        $user->mayIfEquals('manage', 'Book', 'author_id', $user->id);
        $user->mayIfEquals('manage', 'User', 'id', $user->id);
    }
}
```

And then, in your app:

```php
$user = Auth::user();
$book = Book::find($bookId);

if ($user->can('manage', $book))
{
    $book->fill(Input::get('book'));

    // et cetera
}
```

## Philosophy

## Installation

## Usage

## License