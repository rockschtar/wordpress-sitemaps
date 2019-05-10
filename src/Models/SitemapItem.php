<?php
/**
 * Created by PhpStorm.
 * User: StefanHelmer
 * Date: 07.03.2017
 * Time: 16:48
 */

namespace Rockschtar\WordPress\Sitemaps\Models;


class SitemapItem extends SitemapIndexItem {

    private $prioritiy;
    private $change_frequency;
    private $location;

    /**
     * SitemapItem constructor.
     * @param string $location
     * @param int $last_modified_timestamp
     * @param float $prioritiy
     * @param string $change_frequency
     */
    public function __construct(string $location, int $last_modified_timestamp,  float $prioritiy, string $change_frequency) {
        $this->last_modified_timestamp = $last_modified_timestamp;
        $this->location = $location;
        $this->prioritiy = $prioritiy;
        $this->change_frequency = $change_frequency;
        parent::__construct('', $last_modified_timestamp, 0);
    }

    /**
     * @return float
     */
    public function getPrioritiy(): float {
        return $this->prioritiy;
    }

    /**
     * @param float $prioritiy
     */
    public function setPrioritiy(float $prioritiy): void {
        $this->prioritiy = $prioritiy;
    }

    /**
     * @return string
     */
    public function getChangeFrequency(): string {
        return $this->change_frequency;
    }

    /**
     * @param string $change_frequency
     */
    public function setChangeFrequency(string $change_frequency): void {
        $this->change_frequency = $change_frequency;
    }

    /**
     * @return string
     */
    public function getLocation(): string {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation(string $location): void {
        $this->location = $location;
    }
}