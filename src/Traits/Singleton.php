<?php
/**
 * Created by PhpStorm.
 * User: fredbradley
 * Date: 14/06/2018
 * Time: 10:23
 */

namespace StudentRadio\AwardWinners\Traits;


trait Singleton {

	protected static $instance;

	final public static function instance() {

		return isset( static::$instance ) ? static::$instance : static::$instance = new static;
	}

	final public static function clean() {

		return static::$instance = new static;
	}

	final private function __construct() {

		static::init();
	}

	protected static function init() {
	}

	final private function __wakeup() {
	}

	final private function __clone() {
	}

}
