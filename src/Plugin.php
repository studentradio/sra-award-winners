<?php /** @noinspection ALL */

	namespace StudentRadio\AwardWinners;

	use StudentRadio\AwardWinners\Importers\AwardsOneImporter;
	use StudentRadio\AwardWinners\Importers\Handlers\WPFileUploadHandler;

	/**
	 * Class Plugin
	 *
	 * @package StudentRadio\AwardWinners
	 */
	class Plugin extends BaseController
	{
		const PLUGIN_NAME = 'sra-award-winners';
		CONST POST_TYPE_KEY = 'sra-award-winner';
		/**
		 * @var
		 */
		private $post_type;

		/**
		 * Plugin constructor.
		 */
		public function __construct()
		{
			parent::__construct();
			$this->runUpdateChecker(self::PLUGIN_NAME);
			if (is_admin()) {
				add_action('admin_menu', [new Admin(), 'register_my_custom_menu_page']);
			}
			add_action('post_updated', [$this, 'on_delete_winner'], 10, 3);
		}


		/**
		 * @param $post_ID
		 * @param $post_after
		 * @param $post_before
		 *
		 * @return bool|void
		 * @throws \Exception
		 */
		public function on_delete_winner($post_ID, $post_after, $post_before)
		{
			// We check if the global post type isn't ours and just return
			if ($post_after->post_type !== (string)$this->post_type) {
				return;
			}

			if ($post_after->post_status == "trash") {
				error_log("Found in Trash");
				try {
					// My custom stuff for deleting my custom post type here
					$upload_folder = WPFileUploadHandler::getUploadFolder($post_ID);
					array_map('unlink', glob($upload_folder . "/*.*"));
					rmdir($upload_folder);

					return true;
				} catch (\Exception $e) {
					throw new \Exception($e->getMessage(), $e->getCode());

					return false;
				}
			}
			if ($post_before->post_status == "trash") {
				// TODO: write Restore method (can base it on the year taxonomy and reimport the entire year)?
			}
		}

		/**
		 *
		 */
		public function setupPlugin()
		{
			$this->createCustomPostType(self::POST_TYPE_KEY)->register();
		}

		/**
		 * @param string $post_type_key
		 *
		 * @return \FredBradley\CranleighCulturePlugin\CustomPostType
		 */
		private function createCustomPostType(string $post_type_key)
		{

			$this->post_type = new CustomPostType($post_type_key);

			return $this->post_type;
		}
	}
