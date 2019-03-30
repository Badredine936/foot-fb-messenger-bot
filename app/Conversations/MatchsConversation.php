<?php

namespace App\Conversations;

use Illuminate\Support\Facades\App;
use App\Api\ApiFootball;
use App\Http\Controllers\Controller;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class MatchsConversation extends Conversation
{
    /**
     * First question
     */
    public function askReason()
    {
	    $data = App::make(ApiFootball::class)->getClassement();
	    $create = [];
      foreach($data as $v) {
        $create[] = Button::create('matchs ' . $v->team_name)->value(strtolower($v->team_name));
      }
      $question = Question::create("Choisissez une équipe pour voir ses matchs !")
          ->fallback('Une erreur est survenue !')
          ->callbackId('ask_reason')
          ->addButtons(array_slice($create, 0, 5));

        return $this->ask($question, function (Answer $answer) {
	        $interline = "\n";
	        $data = App::make(ApiFootball::class)->getResultats();
          if ($answer->isInteractiveMessageReply()) {
            $text = "";
            foreach($data['byTeam'][strtolower($answer->getValue())] as $match) {
	            $date = new \DateTime($match['date']);
	            $text .= $interline;
	            $text .= date_format($date, 'd/m/Y') . ' : ' . $interline
		            . $match['eq1'] . ' ' . $match['score1'] . ' - '
		            . $match['score2'] . ' ' . $match['eq2'] . $interline;
            }
            $this->say($text);
          }else{
            $this->say("Je n'ai pas compris votre réponse ! Veuillez utiliser le menu ou taper une commande connue !");
          }
        });
    }


//	/**
//	 * Texte stop pour mettre fin à la conversation conversation
//	 * @param \BotMan\BotMan\Messages\Incoming\IncomingMessage $message
//	 *
//	 * @return bool
//	 */
//		public function stopsConversation(IncomingMessage $message)
//		{
//			if ($message->getText() == 'stop') {
//				return true;
//			}
//
//			return false;
//		}

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askReason();
    }
}
