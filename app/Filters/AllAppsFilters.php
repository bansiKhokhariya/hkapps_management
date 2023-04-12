<?php

namespace App\Filters;

use Rjchauhan\LaravelFiner\Filter\Filter;

class AllAppsFilters extends Filter
{
    protected $request;
    protected $filters = ['search_app'];

    public function search_app($search_app)
    {
        $this->builder->where('app_name', 'like', "%{$search_app}%")->orWhere('app_packageName', 'like', "%{$search_app}%")->orWhere('developer', 'like', "%{$search_app}%");
    }

}

