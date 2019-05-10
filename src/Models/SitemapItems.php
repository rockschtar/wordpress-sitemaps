<?php
/**
 * Created by PhpStorm.
 * User: StefanHelmer
 * Date: 08.03.2017
 * Time: 11:56
 */

namespace Rockschtar\Wordpress\Sitemaps\Models;

class SitemapItems {

    /**
     * @var SitemapItem[]
     */
    private $items;

    /**
     * SitemapIndexItems constructor.
     * @param $items  SitemapItem[]
     */
    public function __construct(array $items = array()) {
        $this->items = $items;
    }

    /**
     * @param SitemapItem $item
     */
    public function add(SitemapItem $item): void {
        $this->items[] = $item;
    }

    /**
     * @return SitemapItem[]
     */
    public function getItems(): array {
        return $this->items;
    }

}