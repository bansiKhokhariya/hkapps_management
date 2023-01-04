<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GitController extends Controller
{
    public function createRepo(){
        \Log::info("start repo");
        shell_exec(`curl -X POST -H "Accept: application/vnd.github+json" -H "Authorization: Bearer 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" -H "X-GitHub-Api-Version: 2022-11-28" https://api.github.com/user/repos -d '{"name":"demo_repo","description":"This is your first repo!","homepage":"https://github.com","private":false,"is_template":true}'`);
        \Log::info("end repo");
    }

}

//curl -X POST -H "Accept: application/vnd.github+json" -H "Authorization: Bearer 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" -H "X-GitHub-Api-Version: 2022-11-28" https://api.github.com/user/repos -d '{"name":"demo_repo","description":"This is your first repo!","homepage":"https://github.com","private":false,"is_template":true}'

