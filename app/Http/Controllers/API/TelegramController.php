<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Telegram;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function index()
    {
        $telegram = Telegram::all();
        return $telegram;
    }

    public function store(Request $request)
    {

        $telegram = Telegram::find(1);

        if (!is_null($telegram)) {
            $telegram->update([
                'chat_id' => $request->chat_id,
                'telegramBotToken' => $request->telegramBotToken,
            ]);
            return $telegram;
        } else {
            $telegramNew = new Telegram();
            $telegramNew->chat_id = $request->chat_id;
            $telegramNew->telegramBotToken = $request->telegramBotToken;
            $telegramNew->save();
            return $telegramNew;
        }

    }

    public function show()
    {

        $telegram = Telegram::find(1);
        return $telegram;

    }
}
