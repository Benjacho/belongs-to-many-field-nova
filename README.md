# Belongs To Many Field Nova With Dependant

Belongs To Many field to represent many to many relationship in field. This Field allow attaching relationships easily.
Also you can:

- Pass query to the multiple select
- Depends on BelongsTo field
- It is available in index, detail and forms!

![image](https://user-images.githubusercontent.com/11976865/54318738-46290000-45b5-11e9-8ea0-941adb4b79ba.png)

### Installation

```bash
composer require benjacho/belongs-to-many-field
```

### Usage

In the resource you need to pass:

- Method make ('label', 'many to many relationship function name', 'Nova Resource Relationship')

```php
use Benjacho\BelongsToManyField\BelongsToManyField;

public function fields(Request $request){
    return [
        ..., //If you are using with BelongsToMany native Field, put this field after

        BelongsToManyField::make('Role Label', 'roles', 'App\Nova\Role'),
    ];
}
```

### Functions

| Function                      | Param          | default    | description                                                                                                                                                                  |
| ----------------------------- | --------------- | ---------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `optionsLabel`                | String          | 'name'     | If you don't have column 'name' in your relationship table, use this method. This displays in index and detail Ejm (`optionsLabel('full_role_name')`).                                                                                                                  |
| `isAction`                    | Boolean         | true       | This method is when you need this field in actions, this puts height of field in 350px, and converts in action.                                                                                                                                        |
| `setMultiselectProps`         | Array           | []         | this method allows you to set properties for the [vue multiselect component](https://vue-multiselect.js.org/#sub-props)                                                                                                                            |
| `dependsOn`                   | String, String  | null, null | This method allows you to depend on belongsto field, this make an auto query                                                                                                 |
| `canSelectAll`                | String, Boolean | 'Select All', true | This method allows you to have a select all checkbox and display custom message                                                                                              |
| `showAsListInDetail`          | Boolean         | true       | This method allows you to display as list in detail                                                                                             |

- Method optionsLabel('columnName'), this method is when you don't have column 'name' in your table and you want to
  label by another column name. By default it tracks by label 'name'.

IMPORTANT

- If you want to label by another column name when displaying in forms, you need to set the title() method on your
  relationship resource, this method returns an string that is used to label it, also don't forget to add optionsLabel()
  method to show in detail and index.

```php
use Benjacho\BelongsToManyField\BelongsToManyField;

public function fields(Request $request){
    BelongsToManyField::make('Role Label', 'roles', 'App\Nova\Role')->optionsLabel('full_role_name'),
}
```

- To obtain the data that was sent in action:

```php
public function handle(ActionFields $fields, Collection $models)
{
    //note that roles is the many to many relationship function name
    $values = array_column(json_decode(request()->roles, true),'id');
    
    foreach ($models as $model) {
        $model->roles()->sync($values);
    }
}
```

- Method setMultiselectProps($props), this method allows you to set properties for
  the [vue multiselect component](https://vue-multiselect.js.org/#sub-props)

```php
     BelongsToManyField::make('Role Label', 'roles', 'App\Nova\Role')
     ->options(\App\Role::all())
     ->setMultiselectProps([
        'selectLabel' => 'click for select',
        // and others from docs
     ]);
```

- Method dependsOn($dependsOnvalue, $dependsOnKey), This method allows you to depend on belongsto field, this make an
  auto query

```php
     BelongsTo::make('Association', 'association', 'App\Nova\Association'),

     BelongsToManyField::make('Participants', 'participant', 'App\Nova\Participant')
     ->dependsOn('association', 'association_id'),
```

- Method canSelectAll($messageSelectAll), This method allows you to display select all checkbox, if you dont pass
  message default is displayed

```php
     BelongsToManyField::make('Participants', 'participant', 'App\Nova\Participant')
     ->canSelectAll('Seleccionar Todo'),
```

- Method showAsListInDetail(), This method allows you to change the default view to list in detail

```php
     BelongsToManyField::make('Participants', 'participant', 'App\Nova\Participant')
     ->showAsListInDetail(),
```

### Validations

This package implement all Laravel Validations, you need to pass the rules in rules method, rules are listed on laravel
validations rules for arrays*.

```php
use Benjacho\BelongsToManyField\BelongsToManyField;

public function fields(Request $request){
    return [
        ...,
        BelongsToManyField::make('Role Label', 'roles', 'App\Nova\Role')->rules('required', 'min:1', 'max:5', 'size:3', new CustomRule()),
    ];
}
```

![image](https://raw.githubusercontent.com/Benjacho/belongs-to-many-field-nova/master/validation.png)

### Translations

To publish translations:

```
php artisan vendor:publish --provider="Benjacho\BelongsToManyField\FieldServiceProvider" 
```

This package come with the following translation for the vue-multiselect plugin.

- en, ru.

To translate validations use Laravel validation translations.

Credits to: https://github.com/manmohanjit/nova-belongs-to-dependency
