<?php

namespace StudentRadio\AwardWinners;

class Plugin extends BaseController {

	/**
	 * @var
	 */
	private $post_type;

	/**
	 * Plugin constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->runUpdateChecker( 'sra-award-winners' );
	}


	/**
	 *
	 */
	public function setupPlugin() {
		// TODO: Implement setupPlugin() method.

		// TODO: Custom Post Type
		$this->createCustomPostType("sra-award-winner")->register();
	}

	/**
	 * @param string $post_type_key
	 *
	 * @return \FredBradley\CranleighCulturePlugin\CustomPostType
	 */
	private function createCustomPostType( string $post_type_key ) {

		$this->post_type = new CustomPostType( $post_type_key );

		return $this->post_type;
	}
}
