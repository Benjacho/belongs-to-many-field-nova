<?php

namespace Benjacho\BelongsToManyField;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Benjacho\BelongsToManyField\Rules\ArrayRules;
use Laravel\Nova\Fields\ResourceRelationshipGuesser;
use Laravel\Nova\Fields\FormatsRelatableDisplayValues;
use Laravel\Nova\TrashedStatus;
use Illuminate\Support\Str;

class BelongsToManyField extends Field
{
    use FormatsRelatableDisplayValues;

    /**
     * The column that should be displayed for the field.
     *
     * @var \Closure
     */
    public $display;

    public $showOnIndex = true;
    public $showOnDetail = true;
    public $isAction = false;
    public $selectAll = false;
    public $messageSelectAll = 'Select All';
    public $height = '350px';
    public $viewable = true;
    public $showAsList = false;
    public $pivotData = [];

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
    public function __construct($name, $attribute = null, $resource = null)
    {
        parent::__construct($name, $attribute);
        $resource = $resource ?? ResourceRelationshipGuesser::guessResource($name);

        $this->resource = $resource;
        $this->resourceClass = $resource;
        $this->resourceName = $resource::uriKey();
        $this->manyToManyRelationship = $this->attribute;

        // Syncing relationships after saving the main model.
        $this->fillUsing(function ($request, $model, $attribute, $requestAttribute) use ($resource) {
            if (is_subclass_of($model, 'Illuminate\Database\Eloquent\Model')) {
                $model::saved(function ($model) use ($attribute, $request) {
                    $inp = json_decode($request->$attribute, true);
                    if ($inp !== null) {
                        $values = array_column($inp, 'id');
                    } else {
                        $values = [];
                    }
                    if (!empty($this->pivot())) {
                        $values = array_fill_keys($values, $this->pivot());
                    }
                    $model->$attribute()->sync(
                        $values
                    );
                });
                unset($request->$attribute);
            }
        });
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

    public function canSelectAll($messageSelectAll = 'Select All', $selectAll = true)
    {
        $this->selectAll = $selectAll;
        $this->messageSelectAll = $messageSelectAll;
        return $this->withMeta(['selectAll' => $this->selectAll, 'messageSelectAll' => $this->messageSelectAll]);
    }

    public function showAsListInDetail($showAsList = true)
    {
        $this->showAsList = $showAsList;
        return $this->withMeta(['showAsList' => $this->showAsList]);
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

    public function dependsOn($dependsOnField, $tableKey)
    {
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
        parent::resolve($resource, $attribute);

        $this->value = $resource->{$this->attribute}
            ->map(function ($res) {
                return $this->formatDisplayValues($res);
            });
    }


    public function jsonSerialize()
    {
        return array_merge([
            'attribute' => $this->attribute,
            'component' => $this->component(),
            'indexName' => $this->name,
            'name' => $this->name,
            'nullable' => $this->nullable,
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

    public function pivot()
    {
        return $this->pivotData;
    }

    public function setPivot(array $attributes)
    {
        $this->pivotData = $attributes;

        return $this;
    }


    /**
     * Build an attachable query for the field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  bool  $withTrashed
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildAttachableQuery(NovaRequest $request, $withTrashed = false)
    {
        $model = forward_static_call([$resourceClass = $this->resourceClass, 'newModel']);

        $query = $request->first === 'true'
                            ? $model->newQueryWithoutScopes()->whereKey($request->current)
                            : $resourceClass::buildIndexQuery(
                                $request,
                                $model->newQuery(),
                                $request->search,
                                [],
                                [],
                                TrashedStatus::fromBoolean($withTrashed)
                            );

        return $query->tap(function ($query) use ($request, $model) {
            forward_static_call($this->attachableQueryCallable($request, $model), $request, $query);
        });
    }

    /**
     * Get the attachable query method name.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    protected function attachableQueryCallable(NovaRequest $request, $model)
    {
        return ($method = $this->attachableQueryMethod($request, $model))
                    ? [$request->resource(), $method]
                    : [$this->resourceClass, 'relatableQuery'];
    }

    /**
     * Get the attachable query method name.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return string
     */
    protected function attachableQueryMethod(NovaRequest $request, $model)
    {
        $method = 'relatable'.Str::plural(class_basename($model));

        if (method_exists($request->resource(), $method)) {
            return $method;
        }
    }


    /**
     * Formats the display values to show in the different views.
     *
     * @param  mixed $resource
     * @return array
     */
    public function formatDisplayValues($resource)
    {
        return [
            'id' => $resource->getKey(),
            'label' => $this->formatDisplayValue($resource)
        ];
    }
}
