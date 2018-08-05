<?php
/**
 * Created by PhpStorm.
 * User: fredbradley
 * Date: 14/06/2018
 * Time: 12:05
 */

namespace StudentRadio\AwardWinners\Importers\WPObjects;


use StudentRadio\AwardWinners\Importers\Handlers\WPFileUploadHandler;
use StudentRadio\AwardWinners\Traits\Singleton;

class Winner {
	use Singleton;

	private $tax_award_category = "award-category";
	private $tax_station = "award-station";
	private $tax_year = "award-year";

	private $post_type = "sra-award-winner";

	private $post_id;

	public function init()
	{
		// Your __construct code goes here


	}

	public function __construct() {

	}

	public function setYearTaxonomy(int $post_id, int $year) {
		wp_set_object_terms( $post_id, (string) $year, $this->tax_year );
	}
	public function setCategoryTaxonomy(int $post_id, string $category) {
		wp_set_object_terms( $post_id, $category, $this->tax_award_category );
	}
	public function setStationTaxonomy(int $post_id, string $station) {
		wp_set_object_terms( $post_id, $station, $this->tax_station );
	}
	public function ifPostExists() {
		$query = new \WP_Query([
			"posts_per_page" => -1,
			"post_type" => $this->post_type,
			"s" => $this->title,
			'tax_query' => array(
				'relation' => 'AND',
				array(
					'taxonomy' => $this->tax_year,
					'field'    => 'slug',
					'terms'    => $this->year,
				),
				array(
					'taxonomy' => $this->tax_award_category,
					'field'    => 'name',
					'terms'    => $this->Cat_Name,
				),
				array(
					'taxonomy' => $this->tax_station,
					'field'    => 'name',
					'terms'    => $this->station,
				),
			),
		]);

		if ($query->post_count===1) {
			$this->post_id = $query->posts[0]->ID;
			return true;
		} elseif ($query->post_count > 1) {
			throw new \Exception("Found multiple posts", 300);
		}
		return false;
	}
	private function uploadAttachments() {
		try {

			if ( null !== $this->pdf ) {
				// Upload PDF

				$pdf = new WPFileUploadHandler( $this->pdf, $this->title . " PDF Entry", $this->post_id, "pdf" );
				update_post_meta( $this->post_id, "sra_winner_pdf-entry", $pdf->file_path );

			}
			if ( null !== $this->audio ) {
				// Upload Audio

				$audio = new WPFileUploadHandler( $this->audio, $this->title . " Audio Entry", $this->post_id, "audio" );
				update_post_meta( $this->post_id, "sra_winner_audio-entry", $audio->file_path );

			}
			if(isset($audio->file_path)) {
				echo "<br />FOUND FILE: ".$audio->file_path."<br />";
			}
			if (isset($pdf->file_path)) {
				echo "<br />FOUND FILE: ".$pdf->file_path."<br />";
			}
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), $e->getCode());
		}
		/*
		foreach ($this->images as $image) {
			if (NULL !== $image) {
				// Upload Image
				if (!has_post_thumbnail($this->post_id)) {
					$image = new WPFileUploadHandler( $image, $this->title . " Image", $this->post_id, "image" );
					set_post_thumbnail( $this->post_id, $image->attachment->ID );
				}
			}
		}*/

	}
	public function create(\StudentRadio\AwardWinners\Importers\ApiObjects\Winner $array, int $year) {
		$this->year = $year;
		foreach($array as $key => $value) {
			$this->$key = $value;
		}
		$post_data = [
			'post_title' => $this->title,
			'post_type' => $this->post_type,
		];
		try {
			if ( $this->ifPostExists() === true ) {
				$this->post_id = wp_update_post(
					array_merge(
						[
							'ID' => $this->post_id
						],
						$post_data
					)
				);
			} else {
				$this->post_id = wp_insert_post( $post_data, true );
			}
			echo 'GOT HERE';
			$this->uploadAttachments();
			echo 'GO THERE TOO';
			update_post_meta($this->post_id, "sra_winner_prize", $this->prize);
			update_post_meta($this->post_id, "sra_winner_written-entry", $this->written);
			$this->setYearTaxonomy($this->post_id, $year);
			$this->setCategoryTaxonomy($this->post_id, $this->Cat_Name);
			$this->setStationTaxonomy($this->post_id, $this->station);

		} catch (\Exception $exception) {
			throw new \Exception($exception->getMessage(), $exception->getCode());
		}



	}


}
