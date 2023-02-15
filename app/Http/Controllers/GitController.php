<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GitController extends Controller
{

    public function googleAppRes($packageName){

        $gPlay = new \Nelexa\GPlay\GPlayApps();
        $newApp = $gPlay->getNewApps();



        return $newApp;
    }


}


