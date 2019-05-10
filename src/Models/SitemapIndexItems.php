<?php
namespace Rockschtar\Wordpress\Sitemaps\Models;

/**
 * Class SitemapIndexItems
 * @package ValidIO\WordPressUtils\Models
 */
class SitemapIndexItems {

    /**
     * @var SitemapIndexItem[]
     */
    private $items;

    /**
     * SitemapIndexItems constructor.
     * @param $items  SitemapIndexItem[]
     */
    public function __construct(array $items = array()) {
        $this->items = $items;
    }

    /**
     * @param SitemapIndexItem $item
     */
    public function add(SitemapIndexItem $item) {
        $this->items[] = $item;
    }

    /**
     * @return SitemapIndexItem[]
     */
    public function getItems() {
        return $this->items;
    }
}