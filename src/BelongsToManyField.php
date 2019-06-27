<?php

namespace Benjacho\BelongsToManyField;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Benjacho\BelongsToManyField\Rules\ArrayRules;

class BelongsToManyField extends Field
{
    public $showOnIndex = false;
    public $showOnDetail = false;
    public $isAction = false;
    public $height = '350px';
    /**
     * The field's component.
     *
     * @var string
     */
    
    public $component = 'BelongsToManyField';
    
    public $relationModel;

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  string|null  $resource
     * @return void
     */
    //Code by @drsdre
    public function __construct($name, $attribute = null, $resource = null)
    {
        parent::__construct($name, $attribute);
        $resource = $resource ?? ResourceRelationshipGuesser::guessResource($name);

        $this->resource = $resource;

        $this->resourceClass = $resource;
        $this->resourceName = $resource::uriKey();
        $this->manyToManyRelationship = $this->attribute;
        $this->fillUsing(function($request, $model, $attribute, $requestAttribute) use($resource) {
            if(is_subclass_of($model, 'Illuminate\Database\Eloquent\Model')) {
                $model::saved(function($model) use($attribute, $request) {
                    $values = array_column(json_decode($request->$attribute, true),'id');
                    $model->$attribute()->sync(
                        $values
                    );
                });
                unset($request->$attribute);
            }
        });
    }

    public function optionsLabel(string $optionsLabel="name"){
        return $this->withMeta(['optionsLabel' => $optionsLabel]);
    }

    public function options($options)
    {
        $options = collect($options);
        return $this->withMeta(['options' => $options]);
    }
    
    public function relationModel($model)
    {
        $this->relationModel = $model;
        return $this;
    }

    public function isAction($isAction = true){
        $this->isAction = $isAction;
        return $this->withMeta(['height' => $this->height]);
    }

    public function rules($rules)
    {
        $rules = ($rules instanceof Rule || is_string($rules)) ? func_get_args() : $rules;
        $this->rules = [ new ArrayRules($rules) ];
        return $this;
    }
    
    public function resolve($resource, $attribute = null)
    {
        if($this->isAction){
            parent::resolve($resource, $attribute);
        } else {
            parent::resolve($resource, $attribute);
            $value = json_decode($resource->{$this->attribute});
            if ($value) {
                $this->value = $value;
            }
        }
        
    }
}
