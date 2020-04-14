<?php

namespace Benjacho\BelongsToManyField;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Benjacho\BelongsToManyField\Rules\ArrayRules;
use Laravel\Nova\Fields\ResourceRelationshipGuesser;


class BelongsToManyField extends Field
{
    public $showOnIndex = true;
    public $showOnDetail = true;
    public $isAction = false;
    public $height = '350px';
    public $viewable = true;

    /**
     * The field's component.
     *
     * @var string
     */

    public $component = 'BelongsToManyField';

    public $relationModel;

    public $label = "name";

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  string|null  $resource
     * @return void
     */
    //Code by @drsdre
    public function __construct($name, $attribute = null, $resource = null, $pivotValues=[])
    {
        parent::__construct($name, $attribute);
        $resource = $resource ?? ResourceRelationshipGuesser::guessResource($name);

        $this->resource = $resource;

        $this->resourceClass = $resource;
        $this->resourceName = $resource::uriKey();
        $this->manyToManyRelationship = $this->attribute;
        $this->fillUsing(function ($request, $model, $attribute, $requestAttribute) use ($resource, $pivotValues) {
            if (is_subclass_of($model, 'Illuminate\Database\Eloquent\Model')) {
                $model::saved(function ($model) use ($attribute, $request, $pivotValues) {
                    $inp = json_decode($request->$attribute, true);
                    if (is_array($inp) && !empty($inp))
                        $values = array_fill_keys(array_column($inp, 'id'), $pivotValues);
                    else
                        $values = [];
                    $model->$attribute()->sync(
                        $values
                    );
                });
                unset($request->$attribute);
            }
        });
    }

    public function optionsLabel(string $optionsLabel)
    {
        $this->label = $optionsLabel;
        return $this->withMeta(['optionsLabel' => $this->label]);
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

    public function isAction($isAction = true)
    {
        $this->isAction = $isAction;
        return $this->withMeta(['height' => $this->height]);
    }

    public function viewable($viewable = true)
    {
        $this->viewable = $viewable;
        return $this;
    }

    public function setMultiselectProps($props)
    {
        return $this->withMeta(['multiselectOptions' => $props]);
    }

    public function dependsOn($dependsOnField, $tableKey){
        return $this->withMeta([
            'dependsOn' => $dependsOnField,
            'dependsOnKey' => $tableKey
        ]);
    }

    public function rules($rules)
    {
        $rules = ($rules instanceof Rule || is_string($rules)) ? func_get_args() : $rules;
        $this->rules = [new ArrayRules($rules)];
        return $this;
    }

    public function resolve($resource, $attribute = null)
    {
        if ($this->isAction) {
            parent::resolve($resource, $attribute);
        } else {
            parent::resolve($resource, $attribute);
            $value = json_decode($resource->{$this->attribute});
            if ($value) {
                $this->value = $value;
            }
        }
    }

    public function jsonSerialize()
    {
        return array_merge([
            'attribute' => $this->attribute,
            'component' => $this->component(),
            'indexName' => $this->name,
            'name' => $this->name,
            'nullable' => $this->nullable,
            'optionsLabel' => $this->label,
            'panel' => $this->panel,
            'prefixComponent' => true,
            'readonly' => $this->isReadonly(app(NovaRequest::class)),
            'resourceNameRelationship' => $this->resourceName,
            'sortable' => $this->sortable,
            'sortableUriKey' => $this->sortableUriKey(),
            'stacked' => $this->stacked,
            'textAlign' => $this->textAlign,
            'value' => $this->value,
            'viewable' => $this->viewable,
        ], $this->meta());
    }
}
