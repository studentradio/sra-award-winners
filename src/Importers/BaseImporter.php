<?php

namespace StudentRadio\AwardWinners\Importers;

use StudentRadio\AwardWinners\Traits\Singleton;
use StudentRadio\AwardWinners\Importers\ApiObjects\Winner;
use StudentRadio\AwardWinners\Importers\ApiObjects\Category;

abstract class BaseImporter
{
    use Singleton;

    public $year = null;

    abstract function getWinners();

    abstract function mapWinnersResponse(\stdClass $response);

    public function init()
    {
        // Your __construct code goes here

    }

    public function import()
    {
        foreach ($this->categories as $category) {
            if (! $category instanceof Category) {
                throw new \Exception("Typehint error, this is not a 'Category'!");
            } else {
                echo "====".$category->Cat_Name."====<br />";
                foreach ($category->Winners as $prize => $winner) {
                    if (! $winner instanceof Winner) {
                        throw new \Exception("typehint error, this is not a 'Winner'!");
                    } else {
                        echo "Year: ".$this->year."<br />";
                        echo "Category: ".$category->Cat_Name."<br />";
                        echo "Winner: ".$winner->title."<br />";
                        echo "Prize: ".$prize."<br />";
                        echo "Station: ".$winner->station."<br /><br />";
                        echo "=============<br /><br />";
                        try {
                            WPObjects\Winner::instance()->create($winner, $this->year);
                        } catch (\Exception $exception) {
                            throw new \Exception($exception->getMessage(), $exception->getCode());
                            break;
                        }
                    }
                }
            }
        }
    }

    public static function setYear(int $year)
    {
        self::instance()->year = $year;

        return self::instance();
    }
}
