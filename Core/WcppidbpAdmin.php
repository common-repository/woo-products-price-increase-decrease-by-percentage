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
 * Class WcppidbpAdmin
 * @package WooCommercePPIDBP
 */
class WcppidbpAdmin
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
     * WcppidbpAdmin constructor.
     */
    public function __construct()
    {
        add_filter('plugin_action_links_'.WC_PPIDBP, array($this, 'addActionLink'));
        add_action('admin_menu', array($this, 'registerSubmenuPage'));
        add_action('admin_enqueue_scripts', array($this, 'registerAdminScripts'));
    }

    /**
     * @param $links
     * @return array
     */
    function addActionLink($links )
    {
        $page_url = add_query_arg( array('post_type' => 'product', 'page' => 'woocommerce-ppidbp'), get_admin_url(null, '/edit.php') );
        $support_url = 'mailto:dev@alfiosalanitri.it';

        $plugin_links = array(
            '<a href="' . esc_url($page_url) . '">' . __( 'Start', 'woocommerce-ppidbp' ) . '</a>',
            '<a href="' . $support_url . '">' . __( 'Support', 'woocommerce-ppidbp' ) . '</a>',
        );
        return array_merge( $plugin_links, $links );
    }

    /**
     *
     */
    function registerSubmenuPage() {
        add_submenu_page(
            'edit.php?post_type=product',
            'WooCommerce Products Price Increase / Decrease by Percentage',
            'Increase / Decrease Prices by Percentage',
            'manage_options',
            'woocommerce-ppidbp',
            array($this, 'renderAdminPage')
        );
    }

    /**
     *
     */
    function registerAdminScripts()
    {
        wp_enqueue_script('woocommerce-ppidbp', untrailingslashit(WC_PPIDBP_URL) . '/assets/js/woocommerce-ppidbp.min.js', array('jquery'), WC_PPIDBP_V, true);
        wp_localize_script(
            'woocommerce-ppidbp',
            'woocommerce_ppidbp',
            array(
                'ajax_url' => site_url( 'wp-admin/admin-ajax.php' )
            )
        );
    }

    /**
     *
     */
    function renderAdminPage()
    {
        ?>
        <div id="wrap">
            <div id="poststuff">
                <h1>WooCommerce Products Price Increase / Decrease by Percentage</h1>
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <div class="form-wrapper">
                            <form id="wcppidbp_go" data-action="wcppidbp_increase_decrease" method="post">
                                <?php wp_nonce_field('wcppidbp', 'wcppidbp_nonce', false); ?>
                                <p>
                                    <label for="percentage"><?php _e('Percentage', 'woocommerce-ppidbp');?></label><br/>
                                    <input id="percentage" type="number" min=1" step="0.01" name="percentage" placeholder="12.55" value="10"> %
                                </p>
                                <hr>
                                <p>
                                    <label for="increase"><input id="increase" type="radio" name="type" value="increase" checked="checked"> <?php _e('Increase', 'woocommerce-ppidbp');?>
                                        <label for="decrease"><input id="decrease" type="radio" name="type" value="decrease"> <?php _e('Decrease', 'woocommerce-ppidbp');?>
                                </p>
                                <hr>
                                <p>
                                    <label for="test"><input id="test" type="checkbox" name="test" value="yes" checked="checked"> <?php _e('Test only', 'woocommerce-ppidbp');?> <br/>
                                        <small><?php _e('Check to test. No changes will be applied.', 'woocommerce-ppidbp');?></small></label>
                                </p>
                                <hr>
                                <p>
                                    <button type="submit" class="button button-primary"><?php _e('Start', 'woocommerce-ppidbp');?></button>
                                    <img class="wcppidbp_loading" src="<?php echo esc_url(admin_url('/images/spinner-2x.gif'))?>" width="20" height="20" style="display: none;">
                                </p>
                            </form>
                            <hr>
                            <div id="wcppidbp_results"></div>
                        </div>
                    </div>
                    <div id="postbox-container-1">
                        <div id="side-sortables">
                            <div class="side-inner">
                                <?php
                                $link = esc_url('https://wordpress.org/support/plugin/woocommerce-ppidbp/reviews?rate=5#new-post');
                                printf(__('If you like this plugin please leave us a <a href="%s" target="_blank">★★★★★</a> rating. A huge thanks in advance!', 'woocommerce-ppidbp'), $link);?>
                                <hr>
                                <a href="https://www.alfiosalanitri.it" target="_blank"><?php _e('Credits', 'woocommerce-ppidbp');?></a> | <a href="mailto:dev@alfiosalanitri.it"><?php _e('Support', 'woocommerce-ppidbp');?></a>
                                <hr>
                                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                                <!-- WooCommerce Products Price Increase / Decrease by Percentage -->
                                <ins class="adsbygoogle"
                                     style="display:block"
                                     data-ad-client="ca-pub-5059675554510919"
                                     data-ad-slot="5850373507"
                                     data-ad-format="auto"></ins>
                                <script>
                                    (adsbygoogle = window.adsbygoogle || []).push({});
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            #post-body-content {
                background-color: white;
            }
            #wcppidbp_go {
                display: inline-block;
            }
            .form-wrapper {
                padding: 20px;
            }
        </style>
        <?php
    }

}