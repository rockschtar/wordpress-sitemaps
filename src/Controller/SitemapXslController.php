<?php
/**
 * Created by PhpStorm.
 * User: StefanHelmer
 * Date: 08.03.2017
 * Time: 14:39
 */

namespace Rockschtar\WordPress\Sitemaps\Controller;


use Smarty;

final class SitemapXslController {
    public const SITEMAP_XSL_QUERY_VARS = array('validio_wpu_sitemap_xsl', 'validio_wpu_sitemap_type');
    public const SITEMAP_XSL_NAME = 'validio-wpu-sitemap';

    public function __construct() {
        add_action('template_redirect', array(&$this, 'sitemap_xsl'));
        add_filter('redirect_canonical', array($this, 'redirect_canonical'));
        add_filter('query_vars', array(&$this, 'sitemap_xsl_query_var'));
        add_action('generate_rewrite_rules', array(&$this, 'sitemap_xsl_rewrite_rule'));
    }

    public static function &init() {
        static $instance = false;
        if (!$instance) {
            $instance = new self();
        }
        return $instance;
    }

    final public function sitemap_xsl_query_var($vars) {
        $vars[] = 'validio_wpu_sitemap_xsl';
        $vars[] = 'validio_wpu_sitemap_type';
        return $vars;
    }

    final public function sitemap_xsl_rewrite_rule($wp_rewrite) {

        $new_rules = array(self::SITEMAP_XSL_NAME . '-(.+?)\.xsl$' => 'index.php?validio_wpu_sitemap_xsl=1&validio_wpu_sitemap_type=$matches[1]',);

        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
        return $wp_rewrite->rules;
    }

    final public function sitemap_xsl() {
        $sitemap_xsl = get_query_var('validio_wpu_sitemap_xsl');

        if ('1' === $sitemap_xsl) {
            header('Content-type: text/xml');
            $sitemap_type = get_query_var('validio_wpu_sitemap_type');
            $smarty = new Smarty;

            $smarty->setCompileDir(WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'uploads/wordpress-sitemaps/smarty/templates_c');
            $smarty->setCacheDir(WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'uploads/wordpress-sitemaps/smarty/cache');

            if ($sitemap_type === 'index') {
                $parent_url = '';
                $parent_name = '';
            } else {
                $parent_url = home_url('sitemap-cdweb-phonenumber-index.xml');
                $parent_name =  $sitemap_type . ' Sitemap Index';
            }

            $smarty->setLeftDelimiter('{{');
            $smarty->setRightDelimiter('}}');
            $smarty->assign('sitemap_index_url', $parent_url);
            $smarty->assign('sitemap_index_name', $parent_name);
            $smarty->assign('jquery_url', 'https://code.jquery.com/jquery-2.2.4.min.js');
            $smarty->assign('jquery_tablesorter_url', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.28.5/js/jquery.tablesorter.min.js');

            $smarty->display(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '\tpl\SitemapXsl.tpl');

            exit;
        }
    }

    final public function redirect_canonical($redirect) {
        foreach (self::SITEMAP_XSL_QUERY_VARS as $query_var) {
            if (get_query_var($query_var)) {
                return false;
            }
        }

        return $redirect;
    }
}