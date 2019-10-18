# Belongs To Many Field Nova

Belongs To Many field to represent many to many relationship in field. This Field allow attaching relationships easily, you can pass query to the Multiple Select.

![image](https://user-images.githubusercontent.com/11976865/54318738-46290000-45b5-11e9-8ea0-941adb4b79ba.png)


### Installation
```bash
composer require benjacho/belongs-to-many-field
```

## Deprecation
Method relationModel() no more needed, to prevent conflicts it will be there. And trait HasBelongsToMany no more neede too, both will be in repo, but doesn't work.

Method options is not needed anymore.

### Usage

To use in nova 1.0 use 0.3 in nova 2.0 use 0.4 and above.

In the resource you need to pass:

- Method make (label, many to many relationship, Nova Resource Relationship)
- It is available in index, detail and forms!

```php
use Benjacho\BelongsToManyField\BelongsToManyField;

public function fields(Request $request){
    BelongsToManyField::make('Role Label', 'roles', 'App\Nova\Role'),
}
```

Optional

- Method optionsLabel('columnName'), this method is when you don't have column 'name' in your table and you want to label by another column name. By default it tracks by label 'name'


```php
use Benjacho\BelongsToManyField\BelongsToManyField;

public function fields(Request $request){
    BelongsToManyField::make('Role Label', 'roles', 'App\Nova\Role')->optionsLabel('title'),
}
```

- Method isAction(), this method is when you need this field in actions, this puts height of field in 350px, and converts in action.

```php
use Benjacho\BelongsToManyField\BelongsToManyField;

public function fields(Request $request){
    BelongsToManyField::make('Role Label', 'roles', 'App\Nova\Role')->isAction(),
}
```
To obtain the data that is send in action do it: 

```php
public function handle(ActionFields $fields, Collection $models)
{
    // Get the expenseTypes from the request because the Field BelongsToManyField does not send it
    
    $values = array_column(json_decode(request()->roles, true),'id');
    
    foreach ($models as $model) {
        $model->roles()->sync($values);
    }
}
```

- Method setMultiselectProps($props), this method allows you to set properties for the [vue multiselect component](https://vue-multiselect.js.org/#sub-props)

```php
     BelongsToManyField::make('Role Label', 'roles', 'App\Nova\Role')
     ->options(\App\Role::all()
     ->setMultiselectProps([
        'selectLabel' => 'click for select',
        // and others from docs
     ]);
```

### Validations
This package implement all Laravel Validations, you need to pass the rules in rules method, rules are listed on laravel validations rules for arrays*.

```php
use Benjacho\BelongsToManyField\BelongsToManyField;

public function fields(Request $request){
    BelongsToManyField::make('Role Label', 'roles', 'App\Nova\Role')->relationModel(\App\User::class)->rules('required', 'min:1', 'max:5', 'size:3' new CustomRule),
}
```

![image](https://raw.githubusercontent.com/Benjacho/belongs-to-many-field-nova/master/validation.png)

For translations of this validations, use normal laravel validations translations.

### Todo
Implement validations, implement custom rules

### Contributing
-Pull Requests
-Issues
-Or Contact me: christianbfc97@gmail.com