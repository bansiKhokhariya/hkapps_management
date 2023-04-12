<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class DimensionRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // $height=Image::make($value[0])->height();
        // dd($height);

        $validator1 = Validator::make([ $attribute => $value[0] ], [ $attribute => 'dimensions:width=720,height=1280' ]);

        $validator2 = Validator::make([ $attribute => $value[0] ], [ $attribute => 'dimensions:width=1080,height=1920' ]);

        if ($validator1->passes() || $validator2->passes()) {
            return true;
        }

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Dimensions must either be 720x1280 or 1280x1920';
    }
}
