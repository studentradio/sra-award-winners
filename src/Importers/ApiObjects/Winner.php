<?php
/**
 * Created by PhpStorm.
 * User: fredbradley
 * Date: 14/06/2018
 * Time: 11:14
 */

namespace StudentRadio\AwardWinners\Importers\ApiObjects;


class Winner {

	public $Cat_Name;
	public $prize;
	public $title;
	public $station;
	public $images;
	public $pdf;
	public $video;
	public $written;
	public $audio;


	public function __construct($winner, $Cat_Name) {
		$this->station = $winner->entrant->station->name;
		$this->title = $winner->title;
		$this->audio = $winner->audio;
		$this->pdf = $winner->pdf;
		$this->images = $winner->images;
		$this->video = $winner->video;
		$this->written = $winner->written;
		$this->Cat_Name = $Cat_Name;
		$this->prize = $winner->winner_text;
	}
}
