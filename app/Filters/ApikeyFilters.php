<?php

namespace App\Filters;

use Rjchauhan\LaravelFiner\Filter\Filter;

class ApikeyFilters extends Filter
{
    protected $request;
    protected $filters = ['search_app'];

    public function search_app($search_app)
    {
        $this->builder->where('apikey_packageName', 'like', "%{$search_app}%");
    }

}

