<?php

	namespace StudentRadio\AwardWinners\Importers;


	use GuzzleHttp\Client;
	use StudentRadio\AwardWinners\Importers\ApiObjects\Category;

	/**
	 * Class AwardsOneImporter
	 *
	 * This importer is developed to go with Fred Bradley's Version 1 Awards System ("AwardsOne").
	 *
	 * It is recommended to have a different importer child class (that extends BaseImporter) for each
	 * Awards Platform that is needed, rather than to shoehorn all functionality into one importer.
	 *
	 * @package StudentRadio\AwardWinners\Importers
	 */
	class AwardsOneImporter extends BaseImporter
	{

		/**
		 * @var string
		 */
		public $apiBase = "http://api.studentradioawards.co.uk/";
		/**
		 * @var
		 */
		public $endpoint;
		/**
		 * @var
		 */
		private $client;

		/**
		 *
		 */
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

		/**
		 * @return \StudentRadio\AwardWinners\Importers\AwardsOneImporter
		 */
		public function getWinners()
		{
			$this->endpoint = "winners/" . $this->year;

			$this->response = $this->client->request('GET', $this->endpoint);
			$this->code = $this->response->getStatusCode(); // 200
			$this->response = json_decode($this->response->getBody()->getContents());
			$this->response = $this->mapWinnersResponse($this->response);
			unset($this->client);
			unset($this->response);

			return self::instance();
		}

		/**
		 * @param \stdClass $response
		 *
		 * @return \stdClass
		 */
		public function mapWinnersResponse(\stdClass $response)
		{
			$result = $response->result;
			$categories = [];

			foreach ($result as $category) {
				$categories[ $category->Cat_Name ] = new Category($category);
			}
			$this->categories = $categories;

			return $response;
		}

	}
