<?php

namespace Benjacho\BelongsToManyField\Http\Controllers;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class ResourceController
{
    public function index(NovaRequest $request, $parent, $attribute, $dependsOnValue = null, $dependsOnKey = null)
    {
        $resourceClass = $request->newResource();
        $field = $resourceClass
            ->availableFields($request)
            ->where('component', 'BelongsToManyField')
            ->where('attribute', $attribute)
            ->first();

        /**
         * TODO: Include trashed values.
         */
        $queryResult = $field->buildAttachableQuery($request, false);

        if($dependsOnValue){
          $queryResult = $queryResult->where($dependsOnKey, $dependsOnValue);
        }

        return $queryResult->get()
            ->mapInto($field->resourceClass)
            ->filter(function ($resource) use ($request, $field) {
                return $request->newResource()->authorizedToAttach($request, $resource->resource);
            })->map(function ($resource) use ($field) {
                return $field->formatDisplayValues($resource);
            })->values();
    }
}
