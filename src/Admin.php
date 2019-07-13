<?php

	namespace StudentRadio\AwardWinners;

	use StudentRadio\AwardWinners\Importers\AwardsOneImporter;

	/**
	 * Class Admin
	 *
	 * @package StudentRadio\AwardWinners
	 */
	class Admin
	{
		/**
		 * @var string
		 */
		public $filter_value = Plugin::PLUGIN_NAME . '_filter_by_prize';

		/**
		 * Admin constructor.
		 */
		public function __construct()
		{
			add_action('admin_menu', [$this, 'register_my_custom_menu_page']);
			add_filter('manage_' . Plugin::POST_TYPE_KEY . '_posts_columns', [$this, 'edit_columns']);
			add_action('manage_' . Plugin::POST_TYPE_KEY . '_posts_custom_column', [$this, 'populate_columns'], 10, 2);
			add_action('restrict_manage_posts', [$this, 'admin_posts_filter_restrict_manage_posts']);
			add_filter('parse_query', [$this, 'posts_filter']);

		}


		/**
		 *
		 */
		public function admin_posts_filter_restrict_manage_posts()
		{
			$type = Plugin::POST_TYPE_KEY;
			if (isset($_GET['post_type'])) {
				$type = $_GET['post_type'];
			}


			if (Plugin::POST_TYPE_KEY == $type) {
				/*
				 * I don't really get why `array_flip` is nessecary here, but it is!
				 */
				$values = array_flip(Plugin::getPrizesArray(false));
				?>

				<select name="<?php echo $this->filter_value; ?>">
					<option value=""><?php _e('Filter By Prize Type', 'sra'); ?></option>
					<?php
						$current_v = isset($_GET[ $this->filter_value ]) ? $_GET[ $this->filter_value ] : '';
						foreach ($values as $label => $value) {
							printf
							(
								'<option value="%s"%s>%s</option>',
								$value,
								$value == $current_v ? ' selected="selected"' : '',
								$label
							);
						}
					?>
				</select>
				<?php
			}
		}


		/**
		 * @param $query
		 */
		function posts_filter($query)
		{
			global $pagenow;
			$type = Plugin::POST_TYPE_KEY;
			if (isset($_GET['post_type'])) {
				$type = $_GET['post_type'];
			}
			if (Plugin::POST_TYPE_KEY == $type && is_admin() && $pagenow == 'edit.php' && isset($_GET[ $this->filter_value ]) && $_GET[ $this->filter_value ] != '') {
				$query->query_vars['meta_key'] = MetaBoxes::PREFIX . 'prize';
				$query->query_vars['meta_value'] = $_GET[ $this->filter_value ];
			}
		}


		/**
		 * @param array $columns
		 *
		 * @return array
		 */
		public function edit_columns(array $columns): array
		{
			$columns['prize'] = __('Prize');

			return $columns;
		}


		/**
		 * @param string $column
		 * @param int    $post_id
		 */
		public function populate_columns(string $column, int $post_id)
		{
			if ('prize' === $column) {
				echo get_post_meta($post_id, MetaBoxes::PREFIX . 'prize', true);
			}
		}

		/**
		 *
		 */
		public function register_my_custom_menu_page()
		{
			add_submenu_page("edit.php?post_type=" . Plugin::POST_TYPE_KEY, "Importer", "Import Winners", "manage_options", "importer.php", [
				$this,
				'importer_page',
			]);
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

		/**
		 * @param int $year
		 *
		 * @return bool
		 * @throws \Exception
		 */
		private function validateForm(int $year): bool
		{
			if ($year > date("Y") || $year < 2000) {
				throw new \Exception("That Year is not accepted", 406);

				return false;
			}

			return true;
		}
	}
