<?php
namespace Paymill\Paymill\Model\Source\Creditcard;

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category Paymill
 * @package Paymill_Paymill
 * @copyright Copyright (c) 2013 PAYMILL GmbH (https://paymill.com/en-gb/)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License
 *          (OSL 3.0)
 */
class Creditcards implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * Define which Creditcard Logos are shown for payment
     *
     * @return array
     */
    public function toOptionArray ()
    {
        $creditcards = array(
                array(
                        'label' => __('Visa'),
                        'value' => 'visa'
                ),
                array(
                        'label' => __('MasterCard'),
                        'value' => 'mastercard'
                ),
                array(
                        'label' => __('American Express'),
                        'value' => 'amex'
                ),
                array(
                        'label' => __('CartaSi'),
                        'value' => 'carta-si'
                ),
                array(
                        'label' => __('Carte Bleue'),
                        'value' => 'carte-bleue'
                ),
                array(
                        'label' => __('Diners Club'),
                        'value' => 'diners-club'
                ),
                array(
                        'label' => __('JCB'),
                        'value' => 'jcb'
                ),
                array(
                        'label' => __('Maestro'),
                        'value' => 'maestro'
                ),
                array(
                        'label' => __('China UnionPay'),
                        'value' => 'china-unionpay'
                ),
                array(
                        'label' => __('Discover Card'),
                        'value' => 'discover'
                ),
                array(
                        'label' => __('Dankort'),
                        'value' => 'dankort'
                )
        );
        return $creditcards;
    }
    
}
