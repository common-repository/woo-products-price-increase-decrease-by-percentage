<?php
/**
 * Created by industria01.it
 * Developer: alfio@industria01.it
 * Date: 04/07/18
 * Time: 15:56
 */

namespace WooCommercePPIDBP;
defined('ABSPATH') or exit;

/**
 * Trait TraitWcppidbpHelper
 * @package WooCommercePPIDBP
 */
trait TraitWcppidbpHelper
{
    /**
     * @return array|\stdClass
     */
    function getProducts()
    {
        $args = array(
            'limit' => -1
        );
        $products = wc_get_products( $args );

        return $products;
    }

    /**
     * @param $n
     * @param $product
     * @param $percentage
     * @param $type
     * @param bool $testOnly
     * @return string
     */
    private function updateProductPrice($n, $product, $percentage, $type, $testOnly = true)
    {
        #quali prezzi devo aggiornare
        $pricesToUpdate = array('regular_price', 'sale_price', 'price');

        $html = '<tr>';

        #valori di default
        $productID = $product->get_id();
        $changedMessage = $testOnly ? '<span class="dashicons dashicons-smiley"></span><br/> ' . __('Test', 'woocommerce-ppidbp') : '';

        #n
        $html .= '<td>';
        $html .= $n;
        $html .= '</td>';

        #thumb
        $html .= '<td>';
        $thumbnail = wp_get_attachment_image($product->get_image_id(), array(50,50));
        $html .= $thumbnail ? $thumbnail : wc_placeholder_img(array(50,50));
        $html .= '</td>';

        #id
        $html .= '<td>';
        $html .= $productID;
        $html .= '</td>';

        #name
        $html .= '<td>';
        $html .= $product->get_name();
        $html .= '</td>';

        #type
        $html .= '<td>';
        $html .= $product->get_type();
        $html .= '</td>';

        #price
        $html .= '<td>';

        #preparo una array con i prezzi che sono stati aggiornati
        $pricesChanged = array();
        #Ciclo i prezzi e li aggiorno

        $html .= '<table>';
        foreach ($pricesToUpdate as $priceProp) {

            $html .= '<tr>';

            $price = call_user_func(array($product, 'get_' . $priceProp));

            if($price !== '' || $price > 0) {

                $numberToAddOrRemove = ($price / 100) * $percentage;

                switch ($type) {
                    case 'increase':
                        $newPrice = $price + $numberToAddOrRemove;
                        break;
                    case 'decrease':
                        $newPrice = $price - $numberToAddOrRemove;
                        break;
                    default:
                        $newPrice = $price;
                }

                if(!$testOnly) {
                    #cambio il prezzo

                    call_user_func(array($product, 'set_' . $priceProp), $newPrice);
                    $save = $product->save();

                    #se ok popolo l'array
                    if(intval($save)) {
                        $pricesChanged[] = $priceProp;
                    } else {
                        $newPrice = $price;
                    }
                }
                $currency = get_woocommerce_currency_symbol();
                $html .= '<td><strong>'.ucwords(str_replace('_', ' ', $priceProp)).':</strong></td>';
                $html .= '<td><code>' . $currency . ' ' .  $price . '</code> <span class="dashicons dashicons-arrow-right-alt"></span> <code>' . $currency . ' ' . $newPrice . '</code></td>';

            }

            $html .= '</tr>';
        }
        $html .= '</table>';

        $html .= '</td>';

        #risultato
        $html .= '<td>';
        if(!$testOnly) {
            if(empty($pricesChanged)) {
                $changedMessage = '<div style="color: indianred;"><span class="dashicons dashicons-no-alt"></span>';
                $changedMessage .= '<strong>'.__('No price changed.', 'woocommerce-ppidbp').'</strong>';
                $changedMessage .= '</div>';
            } else {
                $changedMessage = '<div style="color: forestgreen;"><span class="dashicons dashicons-yes"></span>';
                $changedMessage .= ' ' . __('prices changed: ', 'woocommerce-ppidbp') . ': ';
                $i = 1;
                foreach ($pricesChanged as $priceChanged) {
                    $changedMessage .= '<strong>'.ucwords(str_replace('_', ' ', $priceChanged)).'</strong>';
                    $changedMessage .= $i !== count($pricesChanged) ? ', ' : '';
                    $i++;
                }
                $changedMessage .= '</div>';
            }

        }
        $html .= $changedMessage;
        $html .= '</td>';

        $html .= '</tr>';

        return $html;
    }

    /**
     * @param $percentage
     * @param $type
     * @param bool $testOnly
     * @return string
     * @throws \Exception
     */
    function updatePrices($percentage, $type, $testOnly = true )
    {
        $products = $this->getProducts();
        if(empty($products)) {
            throw new \Exception(__('No Products founds.', 'woocommerce-ppidbp'));
        }

        $html = '';
        if($testOnly) {
            $html .= '<h3 style="color: indianred;">'.__('Test Mode Enabled', 'woocommerce-ppidbp').'</h3>';
            $html .= '<p>'.__('No changes will be applied.', 'woocommerce-ppidbp').'</p>';
        }
        $html .= '<table class="widefat striped">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<td>N.</td>';
        $html .= '<td>Thumb</td>';
        $html .= '<td>'.__('Product ID', 'woocommerce-ppidbp').'</td>';
        $html .= '<td>'.__('Product Name', 'woocommerce-ppidbp').'</td>';
        $html .= '<td>'.__('Product Type', 'woocommerce-ppidbp').'</td>';
        $html .= '<td>'.__('Old Price', 'woocommerce-ppidbp').' <span class="dashicons dashicons-arrow-right-alt"></span> '.__('New Price', 'woocommerce-ppidbp').'</td>';
        $html .= '<td>'.__('Changed', 'woocommerce-ppidbp').'</td>';

        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        #ciclo i prodotti
        $n = 1;
        foreach( $products as $product){
            if($product instanceof \WC_Product_Simple) {
                $html .= $this->updateProductPrice($n, $product, $percentage, $type, $testOnly);
            }
            if($product instanceof \WC_Product_Variable) {
                $available_variations = $product->get_available_variations();

                $nSub = $n + .1;
                foreach ($available_variations as $available_variation) {
                    $variationID = $available_variation['variation_id'];
                    $variation = new \WC_Product_Variation($variationID);
                    $html .= $this->updateProductPrice($nSub, $variation, $percentage, $type, $testOnly);
                    $nSub += .1;
                }
            }
            $n++;
        }
        $html .= '</tbody>';
        $html .= '</table>';

        return $html;
    }
}