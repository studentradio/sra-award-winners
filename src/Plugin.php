<?php

namespace StudentRadio\AwardWinners;
/**
 * Class Plugin
 *
 * @package FredBradley\CranleighCulturePlugin
 */
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
		$this->runUpdateChecker( 'cranleigh-alumni-plugin' );
		add_filter('single_template', array($this, 'limit_access'));
	}

	/**
	 * @param $thing
	 *
	 * @return mixed
	 */
	public function limit_access($thing) {

		if (get_current_user_id() !== 1 && get_post_type() == "alumni") {
			wp_redirect("https://www.google.co.uk/search?q=".urlencode(get_the_title()));
			exit();
		} else {
			return $thing;
		}
	}
	/**
	 *
	 */
	public function setupPlugin() {
		// TODO: Implement setupPlugin() method.

		// TODO: Custom Post Type
		$this->createCustomPostType("Alumni")->register();
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
