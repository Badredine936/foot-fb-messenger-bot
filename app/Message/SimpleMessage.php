<?php


namespace App\Message;

use App\Api\ApiFootball;
use App\Http\Controllers\Controller;
use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;
use BotMan\Drivers\Facebook\Extensions\ListTemplate;
use Illuminate\Support\Facades\App;

class SimpleMessage  extends Controller {


	/**
	 *
	 * Retourne le classement de ligue 1
	 * @param $bot
	 */
	public function classement($bot){
		// récupératiion des données depuis l'api (voir App\Api\ApiFootball)
		$data = App::make(ApiFootball::class)->getClassement();
		$text = 'Voici le classement : ';
		foreach($data as $v) {
			$text .= "\n" . $v->away_league_position . " : " . ucwords($v->team_name, " \t\r\n\f\v-");
		}
		$bot->reply($text);
	}

	/**
	 * Retourne tous les résultats des matchs pour l'équipe demandée
	 * @param $bot
	 */
	public function matchsResults($bot, $equipe){
		// récupératiion des données depuis l'api (voir App\Api\ApiFootball)
		$data = App::make(ApiFootball::class)->getResultats();
		if(!isset($data['byTeam'][strtolower($equipe)])) {
			$bot->reply("l'equipe <$equipe>  n'existe pas... taper equipes pour voir la liste des équipes disponible !");
		}else{
			$text = 'Liste des matchs pour ' . $equipe;
			$count = 0;
			foreach($data['byTeam'][strtolower($equipe)] as $match) {
				$date = new \DateTime($match['date']);
				$text .= date_format($date, 'd/m/Y') . ' : ' . "\n" .$match['eq1'].' '.$match['score1'].' - '.$match['score2'].' '.$match['eq2'] . "\n\n";
				$count++;
			}
			if($text) {
				$bot->reply($text);
			}
		}
	}

	/**
	 * Retourne un message de bienvenue ainsi qu'un petit listing de ce qu'il est possible de faire via le messenger FB
	 * @param $bot
	 */
	public function start($bot){
		$bot->reply("Bonjour " . $bot->getUser()->getFirstName() . " pour consulter les résultats d'une équipe, vous devez écrire le mot matchs suivi du nom de votre équipe !
    \n Exemple: matchs Paris Saint-Germain
    \n\n Vous pouvez également consulter le menu ou écrire le mot 'equipes' pour obtenir la liste des équipes disponible !
    ");
	}

	/**
	 * Retourne un message dans le cas ou la réponse du client n'est pas connu
	 * @param $bot
	 */
	public function fallback($bot){
		$bot->reply("Désolé je ne comprend pas votre demande ! N'hésitez pas à utiliser le menu !");
	}

	/**
	 * Retourne la liste des équipes de ligue 1
	 * @param $bot
	 */
	public function equipes($bot){
		// récupératiion des données depuis l'api (voir App\Api\ApiFootball)
		$data = App::make(ApiFootball::class)->getResultats();
		$text = "Vous pouvez écrire 'matchs' suivi du nom de l'équipe pour voir les resultats d'une équipes.\n
		Exemple : matchs Lille \n\n
		Voici la liste des equipes de ligue 1 : ";
		foreach($data['byTeam'] as $equipe => $matches) {
			$text .= "\n" . ucwords($equipe, " \t\r\n\f\v-");
		}
		$bot->reply($text);
	}
	/**
	 * Test des templates en proposant un lien vers les résultats de ligue 1 sur le site de la FFF
	 * @param $bot
	 */
  public function resultats($bot) {
	  $bot->reply(GenericTemplate::create()
		  ->addImageAspectRatio(GenericTemplate::RATIO_SQUARE)
		  ->addElements([
			  Element::create('Resultat de la ligue 1')
				  ->subtitle('Info football')
				  ->image('https://www.fff.fr/bundles/applicationsonatapage/images/logo.png')
				  ->addButton(ElementButton::create('Voir sur FFF')->url('https://www.fff.fr/championnats/fff/federation-francaise-de-football/2018/350309-ligue-1/phase-1/poule-1/derniers-resultats'))
				  ->addButton(ElementButton::create('Aide')
					  ->payload('aide')->type('postback'))
		  ])
	  );
  }

	/**
	 * Test des templates
	 * @param $bot
	 */
	public function test($bot) {
		$bot->reply(ListTemplate::create()
			->useCompactView()
			->addGlobalButton(ElementButton::create('view more')
				->url('http://niooz.fr')
			)
			->addElement(Element::create('BotMan Documentation')
				->subtitle('All about BotMan')
				//->image('http://botman.io/img/botman-body.png')
				->addButton(ElementButton::create('tell me more')
					->payload('tellmemore')
					->type('postback')
				)
			)
			->addElement(Element::create('BotMan Laravel Starter')
				->subtitle('This is the best way to start with Laravel and BotMan')
				//->image('http://botman.io/img/botman-body.png')
				->addButton(ElementButton::create('visit')
					->url('https://github.com/mpociot/botman-laravel-starter')
				)
			)
		);
	}

}
