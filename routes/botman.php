<?php

use App\Message\SimpleMessage;
use App\Http\Controllers\BotManController;

$botman = resolve('botman');

//Réponse au clic sur le bouton démarrer du Facebook messenger
$botman->hears('Bonjour|Salut|Démarrer', SimpleMessage::class.'@start');

//Récupération des différents résultats de ligue 1
$botman->hears('classement', SimpleMessage::class.'@classement');
$botman->hears('resultats', SimpleMessage::class.'@resultats');
$botman->hears('matchs {equipe}', SimpleMessage::class.'@matchsResults');

//Liste les équipes disponibles pour la commande matchs {equipe}
$botman->hears('equipes', SimpleMessage::class.'@equipes');

//Réponse non reconnu
$botman->fallback(SimpleMessage::class.'@fallback');

//test
$botman->hears('test', SimpleMessage::class.'@test');



//Ceci n'est qu'un test d'une conversation
$botman->hears('aide', BotManController::class.'@startConversation');
$botman->hears('Start conversation', BotManController::class.'@startConversation');