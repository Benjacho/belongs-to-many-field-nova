<?php

use Laravel\Nova\Http\Requests\NovaRequest;

Route::get('/{resource}/options/{relationship}', function(NovaRequest $request, $parent, $relationship) {
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
        })->map(function($resource) {
            return [
                'id' => $resource->id,
                'name' => $resource->title(),
                'value' => $resource->getKey(),
            ];
        })->sortBy('name')
        ->values();
});
