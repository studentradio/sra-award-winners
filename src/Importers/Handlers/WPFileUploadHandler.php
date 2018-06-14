<?php

namespace StudentRadio\AwardWinners\Importers\Handlers;

/**
 * Class WPFileUploadHandler
 *
 * @package StudentRadio\AwardWinners\Importers\Handlers
 */
class WPFileUploadHandler {

	public $attachment_type = null;
	public $attachment_id = null;
	public $attachment = null;
	public $parent_post = null;
	public $file_path = null;

	public static function getUploadFolder(int $post_id, $type='path') {
		$upload_dir   = wp_upload_dir();

		if ( ! empty( $upload_dir['basedir'] ) ) {
			$winner_upload_dir = $upload_dir['basedir'].'/award_winners/post_'.$post_id;
			if ( ! file_exists( $winner_upload_dir ) ) {
				wp_mkdir_p( $winner_upload_dir );
			}
		}

		if ($type==='uri') {
			return	str_replace($upload_dir['basedir'], "", trailingslashit( $winner_upload_dir ));
		}

		return trailingslashit( $winner_upload_dir );
	}
	/**
	 * WPFileUploadHandler constructor.
	 *
	 * @param string      $url
	 * @param string      $desc
	 * @param int         $parent_post_id
	 * @param string|null $attachment_type
	 *
	 * @throws \Exception
	 */
	public function __construct( string $url, string $desc, int $parent_post_id, string $attachment_type=null ) {

		$this->setParentPost( $parent_post_id );
		$this->init();
		$this->setAttachmentType( $attachment_type );
		//$this->upload_attachment( $url, $desc, $attachment_type );
		$this->save_file( $url );
	}

	/**
	 * @param int $parent_post_id
	 *
	 * @throws \Exception
	 */
	private function setParentPost( int $parent_post_id ) {

		$this->parent_post = get_post( $parent_post_id );
		if ( ! $this->parent_post instanceof \WP_Post ) {
			throw new \Exception( "Parent Post Does not exist", 404 );
			die( "Dead after exception thrown. Parent post does not exist" );
		}
	}

	/**
	 *
	 */
	public function init() {

		if ( ! function_exists( 'media_handle_upload' ) ) {
			require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
			require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
			require_once( ABSPATH . "wp-admin" . '/includes/media.php' );
		}
	}

	/**
	 * @param string $attachmentType
	 */
	public function setAttachmentType( string $attachmentType ) {

		$this->attachment_type = $attachmentType;

	}
	public static function test_Upload_elsewhere( string $url ) {
		$upload_location = self::getUploadFolder(1234);
		$tmp = download_url($url);
		$file_array = array();
		preg_match( '/[^\?]+\.(jpg|jpe|jpeg|gif|png|pdf|mp3)/i', $url, $matches );

		$file_array['name'] = basename($matches[0]);
		$file_array['tmp_name'] = $tmp;

		file_put_contents($upload_location.$file_array['name'], file_get_contents($file_array['tmp_name']));
		echo "tried to put the file: ".$file_array['name'];
		echo "<br /><br />";
		var_dump($file_array['tmp_name']);
		die(ABSPATH);
	}

	private function file_exists($dir, $url) {
		$file_name = basename($url);
		if (file_exists($dir.$file_name)) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * @param string      $url
	 * @param bool        $overwrite
	 *
	 * @throws \Exception
	 */
	public function save_file( string $url, bool $overwrite=false) {
		$winner_post_id = $this->parent_post->ID;
		$upload_location = $this->getUploadFolder($winner_post_id);

		if ($this->file_exists($upload_location, $url) && $overwrite===false) {
			$this->file_path = $this->getUploadFolder($winner_post_id, "uri").basename($url);
			return $this->file_path;
		}

		$file = download_url( $url );

		if (is_wp_error( $file )) {
			throw new \Exception("Download failed", 400);
		}

		preg_match( '/[^\?]+\.(jpg|jpe|jpeg|gif|png|pdf|mp3)/i', $url, $matches );

		$file_array['name'] = basename($matches[0]);
		$file_array['tmp_name'] = $file;

		file_put_contents($upload_location.$file_array['name'], file_get_contents($file_array['tmp_name']));

		$this->file_path = $this->getUploadFolder($winner_post_id, "uri").$file_array['name'];
		return $this->file_path;

	}
	/**
	 * @param string $url
	 * @param string $desc
	 *
	 * @throws \Exception
	 */
	public function upload_attachment( string $url, string $desc ) {

		$tmp = download_url( $url );
		if ( is_wp_error( $tmp ) ) {
			throw new \Exception( "Download failed", 400 );
			// download failed, handle error
		}

		$file_array = [];

		// Set variables for storage
		// fix file filename for query strings
		preg_match( '/[^\?]+\.(jpg|jpe|jpeg|gif|png|pdf|mp3)/i', $url, $matches );
		$file_array[ 'name' ]     = basename( $matches[ 0 ] );
		$file_array[ 'tmp_name' ] = $tmp;

		// If error storing temporarily, unlink
		if ( is_wp_error( $tmp ) ) {
			@unlink( $file_array[ 'tmp_name' ] );
			$file_array[ 'tmp_name' ] = '';
		}

		// do the validation and storage stuff
		$id = media_handle_sideload( $file_array, $this->parent_post->ID, $desc );

		// If error storing permanently, unlink
		if ( is_wp_error( $id ) ) {
			@unlink( $file_array[ 'tmp_name' ] );
			throw new \Exception( "There was an error in storing the file in the WP Media Libary", 400 );
		}

		$this->attachment_id = $id;
		$this->attachment    = get_post( $this->attachment_id );

	}


}
