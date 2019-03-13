# Belongs To Many Field Nova

Belongs To Many field to represent many to many relationship in field. This Field allow attaching relationships easily, you can pass query to the Multiple Select.

![image](https://user-images.githubusercontent.com/11976865/54318738-46290000-45b5-11e9-8ea0-941adb4b79ba.png)


### Installation
```bash
composer require benjacho/belongs-to-many-field
```

### Usage

First of all use the trait in the model that you want to attach example User, user want to attach roles

```php
    use Benjacho\BelongsToManyField\HasBelongsToMany;

    class User extends Model {
        use HasBelongsToMany;
    }
```
Then in the resource you need to pass:
- Method make (label, many to many relationship, Nova Resource Relationship) 
- Method options (Here you pass option that you need to render in Multiple Select, you can pass Querys, use get() method for that purpose)
- Method relationModel (Here is a trick to pass the relation that you want to bind) Example User
- You need to pass onlyOnForms only to create/edit functionality


```php
use Benjacho\BelongsToManyField\BelongsToManyField;

public function fields(Request $request){
    BelongsToManyField::make('Role Label', 'roles', 'App\Nova\Role')->options(\App\Role::all())->relationModel(\App\User::class)->onlyOnForms(),
}
```

### Todo
Only display onforms by default, implement validations, implement custom rules

### Contributing
-Pull Requests
-Issues
-Contact me: christianbfc97@gmail.com