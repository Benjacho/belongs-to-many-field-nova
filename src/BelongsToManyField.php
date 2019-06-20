<?php

namespace Benjacho\BelongsToManyField;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class BelongsToManyField extends Field
{
    public $showOnIndex = false;
    public $showOnDetail = false;
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
    }

    public function options($options)
    {
        $options = collect($options);
        return $this->withMeta(['options' => $options]);
    }

    public function column(string $column)
    {
        return $this->withMeta(['column' => $column]);
    }

    public function relationModel($model)
    {
        $this->relationModel = $model;
        return $this;
    }

    public function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        $requestValue = strlen($request[$requestAttribute]) > 2 ? json_decode($request[$requestAttribute]) : [];
        $class = get_class($model);
        $class::saved(function ($model) use ($requestValue, $attribute) {
            $model->syncManyValues($requestValue, $attribute, $this->relationModel);
        });
    }

    public function resolve($resource, $attribute = null)
    {
        parent::resolve($resource, $attribute);
        $value = json_decode($resource->{$this->attribute});
        if ($value) {
            $this->value = $value;
        }
    }
}
