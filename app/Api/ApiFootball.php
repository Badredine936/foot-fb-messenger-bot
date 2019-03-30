<?php

namespace App\Api;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class ApiFootball {


	/**
	 * Retourne les données de classement actuel de ligue 1
	 * @return mixed
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
    public function getClassement() {

        $cacheKey = 'api::classement';


        //Cache::forget($cacheKey);

        if(!$data = Cache::get($cacheKey)) {
            $client = new Client();
            $response = $client->request('GET', config('football.url'), [
                'query' => [
                    'APIkey' => config('football.key'),//récupéation depuis le fichier de config football.php
                    'action' => 'get_standings',
                    'league_id' => 127
                ]
            ]);

            $data = json_decode($response->getBody());

            $data = collect($data)->sortBy('overall_league_position')->values()->all();


            //Mise en cache pour 1h des données pour ne pas répéter les appelles à l'API
            Cache::put($cacheKey, $data, now()->addMinutes(60));
        }

        return $data;
    }

	/**
	 * Retourne les données des résultats de matchs par équipes ou par date
	 *
	 * @return mixed
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getResultats() {

		$cacheKey = 'api::resultats';


		//Cache::forget($cacheKey);

		if(!$data = Cache::get($cacheKey)) {

			$client = new Client();
			$response = $client->request('GET', config('football.url'), [
				'query' => [
					'APIkey' => config('football.key'),//récupéation depuis le fichier de config football.php
					'action' => 'get_events',
					'from' => '2018-08-10',
					'to' => date("Y-m-d"),
					'league_id' => 127
				]
			]);

			$data = json_decode($response->getBody());

			//formatage des données par date et par équipe
			$matches = [];
			foreach($data as $row) {
				$match = [
					'date' => $row->match_date,
					'eq1' => $row->match_hometeam_name,
					'eq2' => $row->match_awayteam_name,
					'score1' => $row->match_hometeam_score,
					'score2' => $row->match_awayteam_score,
				];
				$matches['byDate'][$match['date']][] = $match;
				$matches['byTeam'][strtolower($match['eq1'])][] = $match;
				$matches['byTeam'][strtolower($match['eq2'])][] = $match;
			}
			//Mise en cache pour 1h des données pour ne pas répéter les appelles à l'API
			Cache::put($cacheKey, $matches, now()->addMinutes(60));
		}
		return $data;
	}


}