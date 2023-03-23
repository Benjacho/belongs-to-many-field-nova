<?php

namespace Benjacho\BelongsToManyField\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ArrayRules implements Rule
{
    public $rules = [];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $rules)
    {
        array_push($rules, 'array');
        $this->rules = $rules;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $input = [$attribute => json_decode($value, true)];
        $this->rules = [$attribute => $this->rules];
        $validator = Validator::make($input, $this->rules, $this->messages($attribute));
        $this->message = $validator->errors()->get($attribute);

        return $validator->passes();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }

    public function messages($attribute)
    {
        return [
            /*   "size" => __('Select exactly') . ' :size',
              "min" => __('Select a minimum of') . ' :min',
              "max" => __('Select a maximum of') . ' :max', */
        ];
    }
}
