<?php
/**
 * Created by PhpStorm.
 * User: fredbradley
 * Date: 14/06/2018
 * Time: 10:25
 */

namespace StudentRadio\AwardWinners\Importers;


use GuzzleHttp\Client;
use StudentRadio\AwardWinners\Importers\ApiObjects\Category;

class AwardsOneImporter extends BaseImporter {

	public $year;
	public $apiBase = "http://api.studentradioawards.co.uk/";
	public $endpoint;
	public $categories;
	private $client;
	public function init()
	{
		$client = new Client([
			// Base URI is used with relative requests
			'base_uri' => $this->apiBase,
			// You can set any number of default request options.
			'timeout'  => 2.0,
		]);

		$this->client = $client;

	}

	public function getWinners() {
		$this->endpoint = "winners/".$this->year;

		$this->response = $this->client->request('GET', $this->endpoint);
		$this->code = $this->response->getStatusCode(); // 200
		$this->response = json_decode($this->response->getBody()->getContents());
		$this->response = $this->mapWinnersResponse($this->response);
		unset($this->client);
		unset($this->response);

		return self::instance();
	}
	public function mapWinnersResponse(\stdClass $response) {
		$result = $response->result;
		$categories = [];

		foreach ($result as $category ) {
			$categories[$category->Cat_Name] = new Category($category);
		}
		$this->categories = $categories;

		return $response;
	}

}
