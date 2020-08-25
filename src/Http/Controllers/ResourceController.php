<?php

namespace Benjacho\BelongsToManyField\Http\Controllers;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\FieldCollection;

class ResourceController
{
    public function index(NovaRequest $request, $parent, $relationship, $optionsLabel, $dependsOnValue = null, $dependsOnKey = null)
    {
        $resourceClass = $request->newResource();
        $fieldCollection = $resourceClass->availableFields($request);
        $field = $fieldCollection->reduce(function ($carry, $item) {
            if ($item->component === 'nova-dependency-container') {
                $carry = $carry->merge($item->meta()['fields']);
            } else {
                $carry->push($item);
            }
            return $carry;
        }, FieldCollection::make([]))->where('component', 'BelongsToManyField')
            ->where('attribute', $relationship)
            ->first();
        $query = $field->resourceClass::newModel();

        $queryResult = $field->resourceClass::relatableQuery($request, $query);

        if($dependsOnValue){
          $queryResult = $queryResult->where($dependsOnKey, $dependsOnValue);
        }
        return $queryResult->get()
            ->mapInto($field->resourceClass)
            ->filter(function ($resource) use ($request, $field) {
                return $request->newResource()->authorizedToAttach($request, $resource->resource);
            })->map(function ($resource) use ($optionsLabel) {
                return [
                    'id' => $resource->id,
                    $optionsLabel => $resource->title(),
                    'value' => $resource->getKey(),
                ];
            })
            ->values();
    }
}
