<?php
/**
 * Created by PhpStorm.
 * User: benjamin
 * Date: 2/6/19
 * Time: 5:10 PM
 */

namespace Benjacho\BelongsToManyField;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasBelongsToMany
{

    public function model($relationModel): BelongsToMany
    {
        $model = app($relationModel);
        return $this->belongsToMany($model);
    }

    public function syncManyValues($values, $attribute, $relationModel)
    {
        $arrayIds = array_column($values, 'id');
        $this->model($relationModel)->sync($arrayIds);
    }
}
