<?php

use Benjacho\BelongsToManyField\Http\Controllers\ResourceController;

Route::get('/{resource}/options/{field}/{dependsOnValue?}/{dependsOnKey?}', [ResourceController::class, 'index']);
Route::get('/{resource}/{resourceId}/options/{field}/{dependsOnValue?}/{dependsOnKey?}', [ResourceController::class, 'index']);
