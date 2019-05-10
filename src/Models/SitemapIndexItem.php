<?php
namespace Rockschtar\Wordpress\Sitemaps\Models;



use DateTime;
use Rockschtar\WordPress\DateTimeUtils\DateTimeUtils;

class SitemapIndexItem {

    protected $last_modified_timestamp;
    protected $sitemap_id;
    protected $page_number = null;

    /**
     * SitemapIndexItem constructor.
     * @param string $sitemap_id
     * @param int $last_modified_timestamp
     * @param int|null $page_number
     */
    public function __construct(string $sitemap_id, int $last_modified_timestamp, ?int $page_number = null) {
        $this->last_modified_timestamp = $last_modified_timestamp;
        $this->sitemap_id = $sitemap_id;
        $this->page_number = $page_number;
    }

    /**
     * @return mixed
     */
    public function getLastModifiedTimestamp() {
        return $this->last_modified_timestamp;
    }

    /**
     * @param mixed $last_modified_timestamp
     */
    public function setLastModifiedTimestamp($last_modified_timestamp): void {
        $this->last_modified_timestamp = $last_modified_timestamp;
    }


    public function getLastModifiedDateTime() : DateTime {
        return DateTimeUtils::convertTimestampToWordPressDateTime($this->getLastModifiedTimestamp());
    }

    public function getLastModifiedDateW3C() : string {
        $datetime = $this->getLastModifiedDateTime();
        return $datetime->format(DATE_W3C);
    }

    /**
     * @return string
     */
    public function getSitemapId(): string {
        return $this->sitemap_id;
    }

    /**
     * @param string $sitemap_id
     */
    public function setSitemapId(string $sitemap_id): void {
        $this->sitemap_id = $sitemap_id;
    }

    /**
     * @return int|null
     */
    public function getPageNumber(): ?int {
        return $this->page_number;
    }

    /**
     * @param int|null $page_number
     */
    public function setPageNumber($page_number): void {
        $this->page_number = $page_number;
    }

}