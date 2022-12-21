<?php

namespace App\Filters;

use Rjchauhan\LaravelFiner\Filter\Filter;

class AppFilters extends Filter
{
    protected $request;
    protected $filters = ['search_app'];

    public function search_app($search_app)
    {
        $this->builder->where('package_name', 'like', "%{$search_app}%")->orWhere('title', 'like', "%{$search_app}%")->orWhere('developer', 'like', "%{$search_app}%");
    }

}

