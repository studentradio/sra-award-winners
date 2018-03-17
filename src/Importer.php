<?php
/**
 * Created by PhpStorm.
 * User: fredbradley
 * Date: 17/03/2018
 * Time: 10:50
 */

namespace StudentRadio\AwardWinners;


class Importer {
	public function __construct($post) {
		$this->setData($post);

		$this->post_id = $this->addPost($post);

		$this->setTaxonomy($this->year, "award-year");
		$this->setTaxonomy($this->station, "award-station");
	}

	private function setTaxonomy($term_slug, $taxonomy) {
		$term_taxonomy_ids = wp_set_object_terms( $this->post_id, $term_slug, $taxonomy );

	}

	private function setData($post) {
		$this->post_title = $post['post_title'];
		$this->year = $post['year'];
		$this->station = $post['station'];
		$this->category = $post['category'];
	}

	private function addMeta(string $meta_id, string $value) {
		update_post_meta($this->post_id, $meta_id, $value);
	}

	private function addPost($post) {
		if ( wp_doing_ajax() || wp_doing_cron() )
			return;
		return wp_insert_post($this->postDataArr());
	}

	private function postDataArr() {

		return [
			"post_type" => "sra-award-winner",
			"post_status" => "publish",
			"post_title" => $this->post_title,

			"post_author" => 1,
		];

	}
}
