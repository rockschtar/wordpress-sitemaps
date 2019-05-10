<?php
/**
 * Created by PhpStorm.
 * User: StefanHelmer
 * Date: 07.03.2017
 * Time: 14:48
 */

namespace Rockschtar\Wordpress\Sitemaps\Controller;

use Rockschtar\Wordpress\Sitemaps\Models\SitemapIndexItems;
use Thepixeldeveloper\Sitemap\Drivers\XmlWriterDriver;
use Thepixeldeveloper\Sitemap\Sitemap;
use Thepixeldeveloper\Sitemap\SitemapIndex;
use Thepixeldeveloper\Sitemap\Urlset;


abstract class SitemapController {
	private static $_instances;


	final public function __construct() {
		add_filter('query_vars', array($this, 'query_vars'));
		add_action('template_redirect', array(&$this, 'sitemap_index'));
		add_action('template_redirect', array($this, 'sitemap_page'));
		add_filter('redirect_canonical', array($this, 'redirect_canonical'));
		add_filter('wpseo_sitemap_index', array($this, 'extend_wpseo_sitemap_index'));
		add_action('generate_rewrite_rules', array($this, 'generate_rewrite_rules'));

		if($this->includeXsl()) {
            SitemapXslController::init();
        }

	}

	final public static function &init() {
		/** @noinspection ClassConstantCanBeUsedInspection */
		$class = \get_called_class();
		if(!isset(self::$_instances[ $class ])) {
			self::$_instances[ $class ] = new $class();
		}

		return self::$_instances[ $class ];
	}

	abstract public function includeXsl() : bool;

	abstract public function sitemapName() : string;

	abstract protected function getSitemapIndexItems(): SitemapIndexItems;

	abstract protected function getSitemapPageUrlSet(int $page_number = 1): Urlset;

	final public function extend_wpseo_sitemap_index($xml_string): string {

		$sitemap = $this->getSitemapIndexItems();
		$sitemap_index_item_pattern = '<sitemap><loc>%s</loc><lastmod>%s</lastmod></sitemap>';

		$location_pattern = get_home_url(null, 'sitemap-%s-%d.xml');

		foreach($sitemap->getItems() as $item) {
			$xml_string .= sprintf($sitemap_index_item_pattern, sprintf($location_pattern, $item->getSitemapId(), $item->getPageNumber()), $item->getLastModifiedDateW3C());
		}

		return $xml_string;
	}

	final public function generate_rewrite_rules($wp_rewrite): array {

		$page_url_pattern = 'sitemap-%s-([0-9]{1,})\.xml$';
		$page_query_pattern = 'index.php?%s=$matches[1]';

		$index_url_pattern = 'sitemap-%s-index\.xml$';
		$index_query_pattern = 'index.php?%s=1';

		$new_rules = array(
			sprintf($index_url_pattern, $this->sitemapName()) => sprintf($index_query_pattern, $this->getQueryVarIndex()),
			sprintf($page_url_pattern, $this->sitemapName()) => sprintf($page_query_pattern, $this->getQueryVarPage()),
		);

		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;

		return $wp_rewrite->rules;
	}

	final public function query_vars($vars): array {
		return array_merge($this->getQueryVars(), $vars);
	}

	final public function sitemap_index(): void {

		$is_sitemap_index = get_query_var($this->getQueryVarIndex());

		if('1' === $is_sitemap_index) {
			header('Content-type: text/xml');
			$sitemap = $this->getSitemapIndexItems();

			$sitemap_index = new SitemapIndex();


			$location_pattern = get_home_url(null, 'sitemap-%s-%d.xml');

			foreach($sitemap->getItems() as $item) {
				$location = sprintf($location_pattern, $item->getSitemapId(), $item->getPageNumber());
				$sitemap = new Sitemap($location);
				$sitemap->setLastMod($item->getLastModifiedDateTime());
				$sitemap_index->add($sitemap);
			}

			$driver = new XmlWriterDriver();
			$xsl_path = home_url(SitemapXslController::SITEMAP_XSL_NAME . '-index.xsl');
			$driver->addProcessingInstructions('xml-stylesheet', 'type="text/xsl" href="' . $xsl_path . '"');
			$sitemap_index->accept($driver);

			echo $driver->output();
			exit;
		}
	}

	final public function sitemap_page(): void {
		$page_number = get_query_var($this->getQueryVarPage());

		if(!empty($page_number) && is_numeric($page_number)) {
			header('Content-type: text/xml');
			$url_set = $this->getSitemapPageUrlSet($page_number);

			$driver = new XmlWriterDriver();
			$xsl_path = home_url(SitemapXslController::SITEMAP_XSL_NAME . '-page.xsl');
			$driver->addProcessingInstructions('xml-stylesheet', 'type="text/xsl" href="' . $xsl_path . '"');
			$url_set->accept($driver);
			echo $driver->output();

			exit;
		}
	}

	final public function redirect_canonical($redirect): bool {
		$query_var = get_query_var($this->getQueryVarPage());
		if(!empty($query_var)) {
			return false;
		}

		return $redirect;
	}


	private function getSitemapKey(): string {
		return sanitize_key($this->sitemapName());
	}

	private function getQueryVarPage(): string {
		return $this->getSitemapKey();
	}

	private function getQueryVarIndex(): string {
		return $this->getSitemapKey() . '-index';
	}

	private function getQueryVars(): array {
		return [$this->getQueryVarIndex(), $this->getQueryVarPage()];
	}
}