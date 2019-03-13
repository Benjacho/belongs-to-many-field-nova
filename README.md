# Belongs To Many Field Nova

Belongs To Many field to represent many to many relationship in field. This Field allow attaching relationships easily, you can pass query to the Multiple Select.

![alt text](https://raw.githubusercontent.com/benjacho/belongs-to-many-field-nova/master/src/image.png)


<h1 align="center">INSTALLATION</h1>
<p>composer require benjacho/belongs-to-many-field</p>

<h1 align="center">USING IT (EXAMPLE)</h1>

<p>- First of all use it as normal Field you nees to pass two options,  one options is the data inside of the select, second option is the model that is relationed</p>
<p>- Use it in the Resource that you need to sync options</p>


```php
    use Benjacho\BelongsToManyField\HasBelongsToMany;

    use HasBelongsToMany;
```

```php
use Benjacho\BelongsToManyField\BelongsToManyField;

public function fields(Request $request){
    BelongsToManyField::make('Tipo de Gasto', 'expenseTypes', 'App\Nova\ExpenseType')->options(\App\ExpenseType::all())->relationModel(\App\ExpenseType::class)->onlyOnForms(),
}
```
