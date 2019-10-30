<?php

use Laravel\Nova\Http\Requests\NovaRequest;

Route::get('/{resource}/options/{relationship}/{optionsLabel}', function (NovaRequest $request, $parent, $relationship, $optionsLabel) {
    $resourceClass = $request->newResource();

    $field = $resourceClass
        ->availableFields($request)
        ->where('component', 'BelongsToManyField')
        ->where('attribute', $relationship)
        ->first();

    $query = $field->resourceClass::newModel();

    return $field->resourceClass::relatableQuery($request, $query)->get()
        ->mapInto($field->resourceClass)
        ->filter(function ($resource) use ($request, $field) {
            return $request->newResource()->authorizedToAttach($request, $resource->resource);
        })->map(function ($resource) use ($optionsLabel) {
            return [
                'id' => $resource->id,
                $optionsLabel => $resource->title(),
                'value' => $resource->getKey(),
            ];
        })->sortBy('name')
        ->values();
});
