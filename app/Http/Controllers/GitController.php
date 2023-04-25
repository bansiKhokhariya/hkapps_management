<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GitController extends Controller
{

    public function googleAppRes($packageName)
    {

        $gPlay = new \Nelexa\GPlay\GPlayApps();
        $newApp = $gPlay->getNewApps();


        return $newApp;
    }

    public function sendTelegramMessage()
    {

        $app_details_link = "https://api.telegram.org/bot6025847479:AAFEsqZ0sNZxD12hzUx-beI88J13U58NuYs/sendMessage?chat_id=5378656582&text=app is removed";
        $res = Http::get($app_details_link);
        if ($res->status() == 200) {

        } else {

        }
    }


}


