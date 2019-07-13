<?php


	namespace StudentRadio\AwardWinners;


	use StudentRadio\AwardWinners\Importers\AwardsOneImporter;

	class Admin
	{
		/**
		 *
		 */
		public function register_my_custom_menu_page()
		{
			add_submenu_page("edit.php?post_type=sra-award-winner", "Importer", "Import Winners", "manage_options", "importer.php", [
				$this,
				'importer_page',
			]);
		}

		/**
		 * @param int $year
		 *
		 * @return bool
		 * @throws \Exception
		 */
		private function validateForm(int $year)
		{
			if ($year > date("Y") || $year < 2000) {
				throw new \Exception("That Year is not accepted", 406);

				return false;
			}

			return true;
		}

		/**
		 * @throws \Exception
		 */
		public function importer_page()
		{

			if ($_POST && isset($_POST['year']) && $this->validateForm($_POST['year']) === true) {
				set_time_limit(0);
				ini_set('max_execution_time', 1800);
				AwardsOneImporter::setYear($_POST['year'])->getWinners()->import();
				echo 'Ran';
			}

			?>
			<div class="wrap">
				<h2>Import Winners from an API</h2>
				<h3>Please set which year you want to import?</h3>
				<form method="post">
					<input required="required" type="number" name="year" placeholder="<?php echo date("Y") - 1; ?>"/>
					<input type="submit" value="Import" class="button-primary"/>
				</form>

			</div>
			<?php

		}
	}
