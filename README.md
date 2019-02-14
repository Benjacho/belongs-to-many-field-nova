# belongs-to-many-field-nova
Belongs To Many field Laravel nova to represent many to many relationship in field.

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
