<?php

namespace Benjacho\BelongsToManyField\Http\Controllers;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class ResourceController
{
    public function index(NovaRequest $request)
    {
        $resourceClass = $request->newResource();
        $field = $resourceClass
            ->availableFields($request)
            ->where('component', 'BelongsToManyField')
            ->where('attribute', $request->field)
            ->first();

        /**
        * TODO: Include trashed values.
        */
        $query = $field->buildAttachableQuery($request, false);

        if ($request->dependsOnValue) {
            $query = $query->where(
                $request->dependsOnKey,
                $request->dependsOnValue
            );
        }

        return $query->get()
            ->mapInto($field->resourceClass)
            ->filter(function ($resource) use ($request, $field) {
                return $request->newResource()->authorizedToAttach($request, $resource->resource);
            })->map(function ($resource) use ($field) {
                return $field->formatDisplayValues($resource);
            })->values();
    }
}
