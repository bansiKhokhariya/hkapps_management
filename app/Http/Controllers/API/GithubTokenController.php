<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AdsProVersion;
use App\Models\GitHubToken;
use Illuminate\Http\Request;

class GithubTokenController extends Controller
{
    public function index()
    {
        $gitHubToken = GitHubToken::find(1);
        return $gitHubToken;
    }

    public function store(Request $request)
    {

        $gitHubToken = GitHubToken::find(1);

        if (!is_null($gitHubToken)) {
            $gitHubToken->update([
                'github_access_token' => $request->github_access_token,
            ]);
            return $gitHubToken;
        } else {
            $gitHubTokenCreate = new GitHubToken();
            $gitHubTokenCreate->github_access_token = $request->github_access_token;
            $gitHubTokenCreate->save();
            return $gitHubTokenCreate;
        }

    }

    public function show()
    {

        $gitHubToken = GitHubToken::find(1);
        return $gitHubToken;

    }
}
