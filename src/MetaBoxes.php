<?php
/**
 * Created by PhpStorm.
 * User: fredbradley
 * Date: 24/07/2017
 * Time: 15:25
 */

namespace StudentRadio\AwardWinners;

class MetaBoxes
{

	/**
	 * @var string
	 */
	CONST PREFIX = "sra_winner_";

	/**
	 * @var array|string
	 */
	private $post_types;

	/**
	 * MetaBoxes constructor.
	 *
	 * @param array|string $types
	 */
	public function __construct($types)
    {
        $this->post_types = $types;

    }


	/**
	 * @param string $id
	 *
	 * @return string
	 */
	private function fieldID(string $id) {
    	return self::PREFIX.$id;
	}


	/**
	 * @param array $meta_boxes
	 *
	 * @return array
	 */
	public function register($meta_boxes) {

        $meta_boxes[] = array(
            "id" => "prize_meta",
            "title" => "Prize Meta",
            "post_types" => $this->post_types,
            "context" => "side",
            "priority" => "high",
            "autosave" => true,
            "fields" => array(
                array(
                    "name" => __("Prize", "cranleigh"),
                    "id" => $this->fieldID("prize"),
                    "type" => "select",
                    "desc" => "Bronze, Silver or Gold",
	                "placeholder" => "",
	                "options" => Plugin::getPrizesArray()

                )
            ),
            'validation' => array(
                'rules'    => array(
                    $this->fieldID("prize") => array(
	                    "required" => true
                    ),
                ),
                // optional override of default jquery.validate messages
                'messages' => array(
                    $this->fieldID("prize") => array(

                    ),
                ),
            ),
        );

        $meta_boxes[] = array(
        	"id" => "award_meta",
	        "title" => "Award Content",
	        "post_types" => $this->post_types,
	        "context" => "normal",
	        "priority" => "high",
	        "autosave" => true,
	        "fields" => array(
	        	array(
	        		"name" => __("Audio", "sra"),
			        "id" => $this->fieldID("audio-entry"),
			        "type" => "file_input",
		        ),
		        array(
		        	"name" => __("PDF Entry", "sra"),
			        "id" => $this->fieldID("pdf-entry"),
			        "type" => "file_input",
		        ),
		        array(
		        	"name" => __("Written Entry", "sra"),
			        "id" => $this->fieldID("written-entry"),
			        "type" => "wysiwyg",
					"options" => array(
						"teeny" => true,
						'textarea_rows' => 10,
						'media_buttons' => false
					)
		        )
	        )
        );
        return $meta_boxes;

    }
}
