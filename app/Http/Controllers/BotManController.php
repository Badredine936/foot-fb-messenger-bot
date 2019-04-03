<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use App\Conversations\MatchsConversation;


class BotManController extends Controller
{

    public function __construct() {

    }
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
	    \Debugbar::disable();
        $botman = app('botman');

        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new MatchsConversation());
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function toto(BotMan $bot)
    {
        $bot->startConversation(new MatchsConversation());
    }


}
