<?php
/**
 * Created by PhpStorm.
 * User: fredbradley
 * Date: 14/06/2018
 * Time: 11:08
 */

namespace StudentRadio\AwardWinners\Importers\ApiObjects;


class Category {
	public $Cat_Name = "";
	public $Winners = [];

	public function __construct($category_result) {
		$this->Cat_Name = $category_result->Cat_Name;
		foreach ($category_result->winners as $winner) {
			$award = $winner->winner_text;
			$this->Winners[$award] = new Winner($winner, $this->Cat_Name);
		}

	}
}
