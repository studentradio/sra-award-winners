<?php /** @noinspection ALL */

/*
Plugin Name: SRA Award Winners
Plugin URI: https://www.studentradio.org.uk
Description: For Award WInners
Version: 1.0
Author: fredbradley
Author URI: https://www.fredbradley.uk
License: MIT
*/

namespace StudentRadio\AwardWinners;

if (! defined('WPINC')) {
    die;
}
require_once plugin_dir_path(__FILE__).'vendor/autoload.php';

$plugin = new Plugin();

if (isset($_POST['apiKey']) && $_POST['apiKey'] === sha1('awards')) {
    add_action('init', function () {
        $import = new Importer($_POST);
    });
}
