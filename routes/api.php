<?php

use Benjacho\BelongsToManyField\Http\Controllers\ResourceController;

Route::get('/{resource}/options/{relationship}/{optionsLabel}', [ResourceController::class, 'index']);
