<?php

namespace StudentRadio\AwardWinners;

use StudentRadio\AwardWinners\Importers\AwardsOneImporter;
use StudentRadio\AwardWinners\Importers\Handlers\WPFileUploadHandler;

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
		add_action( 'admin_menu', array($this,'register_my_custom_menu_page') );

	}

	public function register_my_custom_menu_page() {
		add_submenu_page("edit.php?post_type=sra-award-winner", "Importer", "Import Winners", "manage_options", "importer.php", array($this, 'importer_page'));
	}

	private function validateForm(int $year) {
		if ($year > date("Y") || $year < 2000) {
			throw new \Exception("That Year is not accepted", 406);
			return false;
		}
		return true;
	}

	public function importer_page() {
		if ($_POST && isset($_POST['year']) && $this->validateForm($_POST['year'])===true) {
			AwardsOneImporter::setYear($_POST['year'])->getWinners()->import();
			echo 'Ran';
		}


		?>
		<div class="wrap">
			<h2>Import Winners from an API</h2>
			<h3>Please set which year you want to import?</h3>
			<form method="post">
				<input required="required" type="number" name="year" placeholder="<?php echo date("Y")-1;?>" />
				<input type="submit" value="Import" class="button-primary" />
			</form>

		</div>
<?php


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
