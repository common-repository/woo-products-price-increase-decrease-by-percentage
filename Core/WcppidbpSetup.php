<?php
/**
 * Created by alfiosalanitri.it
 * Developer: dev@alfiosalanitri.it
 * Date: 04/07/18
 * Time: 15:14
 */

namespace WooCommercePPIDBP;
defined('ABSPATH') or exit;

/**
 * Class WcppidbpSetup
 * @package WooCommercePPIDBP
 */
class WcppidbpSetup
{
    /**
     * @var null
     */
    private static $instance = null;

    /**
     * @var string
     */
    private $pluginSlug = 'woocommerce-ppidbp/woocommerce-ppidbp.php';

    /**
     * @var float
     */
    private $phpMinVersion = 5.6;

    /**
     * @return null
     */
    public static function getInstance()
    {
        if(is_null(self::$instance)) self::$instance = new self();
        return self::$instance;
    }

    /**
     * WcppidbpSetup constructor.
     */
    public function __construct()
    {
        if(version_compare(phpversion(), $this->phpMinVersion, '<')) {
            add_action('admin_init', array($this, 'checkPhp'));
        } elseif(!class_exists('WooCommerce')) {
            add_action('admin_init', array($this, 'checkDeps'));
        } else {
            add_action( 'init', array( $this, 'loadTextdomain' ) );
            WcppidbpAdmin::getInstance();
            WcppidbpAjax::getInstance();
        }
    }

    /**
     *
     */
    public function loadTextdomain() {
        load_plugin_textdomain( 'woocommerce-ppidbp', false, dirname( WC_PPIDBP ) . '/languages/' );
    }

    /**
     *
     */
    public function checkPhp()
    {
        add_action('admin_notices', array($this, 'phpVersionMessage'));
        deactivate_plugins( $this->pluginSlug );
    }

    /**
     *
     */
    public function phpVersionMessage()
    {
        echo '<div class="notice notice-error"><p><b>'.sprintf(__('WooCommerce Products Price Increase / Decrease by Percentage requires PHP version %d+. Your version: %d', 'woocommerce-ppidbp'), $this->phpMinVersion, phpversion()).'</b></p></div>';
    }

    /**
     *
     */
    public function checkDeps()
    {
        add_action('admin_notices', array($this, 'mainPluginMissed'));
        deactivate_plugins( $this->pluginSlug );
    }

    /**
     *
     */
    public function mainPluginMissed()
    {
        $mainPlugin = '<a href="'.esc_url(add_query_arg(array('s' => 'woocommerce', 'tab' => 'search', 'type' => 'term'), admin_url('plugin-install.php'))).'">WooCommerce</a>';
        echo '<div class="notice notice-error"><p><b>'.sprintf(__('%s is not active. WooCommerce Products Price Increase / Decrease by Percentage requires WooCommerce to be active.', 'woocommerce-ppidbp'), $mainPlugin).'</b></p></div>';
    }
}