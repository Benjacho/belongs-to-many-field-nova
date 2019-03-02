<?php

namespace Benjacho\BelongsToManyField;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class BelongsToManyField extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'BelongsToManyField';

    public $relationModel;

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

    public function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if (strlen($request[$requestAttribute]) > 2) {
            $requestValue = json_decode($request[$requestAttribute]);
            $class = get_class($model);
            $class::saved(function ($model) use ($requestValue, $attribute) {
                $model->syncManyValues($requestValue, $attribute, $this->relationModel);
            });
        }
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
