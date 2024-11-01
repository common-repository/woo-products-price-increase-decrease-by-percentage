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
 * Class WcppidbpAjax
 * @package WooCommercePPIDBP
 */
class WcppidbpAjax
{
    use TraitWcppidbpHelper;

    /**
     * @var null
     */
    private static $instance = null;

    /**
     * @return null
     */
    public static function getInstance()
    {
        if(is_null(self::$instance)) self::$instance = new self();
        return self::$instance;
    }

    /**
     * WcppidbpAjax constructor.
     */
    public function __construct()
    {
        add_action('wp_ajax_wcppidbp_increase_decrease', array($this, 'increaseDecreasePrices'));
    }

    /**
     *
     */
    function increaseDecreasePrices()
    {
        $response = array();
        try {
            if(!check_ajax_referer('wcppidbp', 'wcppidbp_nonce', false)) {
                throw new \Exception(__('Ajax Request failed.', 'woocommerce-ppidbp'));
            }
            if(!isset($_POST['percentage']) || !floatval($_POST['percentage'])) {
                throw new \Exception(__('Percentage must be a float value.', 'woocommerce-ppidbp'));
            }
            if(!isset($_POST['type']) || !in_array($_POST['type'], array('increase', 'decrease'))) {
                throw new \Exception(__('Choose between increase or decrease.', 'woocommerce-ppidbp'));
            }

            $percentage = floatval($_POST['percentage']);
            $type = sanitize_text_field($_POST['type']);
            $testOnly = isset($_POST['test']) && 'yes' === $_POST['test'] ? true : false;

            $response['results'] = $this->updatePrices($percentage, $type, $testOnly);
        } catch (\Exception $e) {
            $response['results'] = '<div style="color: indianred; font-weight: bold;">'.$e->getMessage().'</div>';
        }
        wp_send_json($response);
    }
}