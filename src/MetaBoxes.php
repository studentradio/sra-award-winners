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
	public $prefix = "alumni_";
	/**
	 * @var int
	 */
	private $startYear;
	/**
	 * @var int
	 */
	private $endYear;

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

        $this->setYears();
    }

	/**
	 *
	 */
	private function setYears() {
    	$this->startYear = 1865;
    	$this->endYear = date("Y");
    }

	/**
	 * @param string $id
	 *
	 * @return string
	 */
	private function fieldID(string $id) {
    	return $this->prefix.$id;
	}

	/**
	 * @return array
	 */
	private function years() {
    	$range = range($this->startYear, date("Y"));
		$output = [];
		foreach ($range as $year):
		    $output[$year] = $year;
    	endforeach;
    	return $output;
	}

	/**
	 * @param array $meta_boxes
	 *
	 * @return array
	 */
	public function register($meta_boxes) {

        $meta_boxes[] = array(
            "id" => "alumni_meta",
            "title" => "Alumni Meta",
            "post_types" => $this->post_types,
            "context" => "side",
            "priority" => "high",
            "autosave" => true,
            "fields" => array(
                array(
                    "name" => __("Exit Year ", "cranleigh"),
                    "id" => $this->fieldID("graduation"),
                    "type" => "number",
                    "desc" => "The Year that the individual left the school.",
	                "options" => $this->years()
                )
            ),
            'validation' => array(
                'rules'    => array(
                    $this->fieldID("graduation") => array(
	                    "range" => [$this->startYear,date("Y")],
	                    "number" => true
                    ),
                ),
                // optional override of default jquery.validate messages
                'messages' => array(
                    $this->fieldID("graduation") => array(
                        'range'  => __( 'Hold up! The person must have left the school since the school\'s inception ('.$this->startYear.') and this year ('.$this->endYear.')', 'cranleigh' ),
	                    'number' => __( 'Must be a year (four digits)', 'cranleigh' )
                    ),
                ),
            ),
        );
        return $meta_boxes;

    }
}
