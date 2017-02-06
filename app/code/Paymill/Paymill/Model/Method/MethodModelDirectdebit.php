<?php
namespace Paymill\Paymill\Model\Method;

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
class MethodModelDirectdebit extends \Paymill\Paymill\Model\Method\MethodModelAbstract
{

    const PAYMENT_METHOD_DIRECTDEBIT_CODE = 'paymill_directdebit';

    /**
     * Magento method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_DIRECTDEBIT_CODE;

    public function __construct (\Magento\Framework\Model\Context $context, 
            \Magento\Framework\Registry $registry, 
            \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory, 
            \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory, 
            \Magento\Payment\Helper\Data $paymentData, 
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, 
            \Magento\Payment\Model\Method\Logger $logger, 
            \Magento\Store\Model\StoreManagerInterface $storeManager, 
            \Paymill\Paymill\Helper\Data $paymillHelper, 
            \Magento\Checkout\Model\Session $checkoutSession, 
            \Magento\Framework\Session\Generic $generic, 
            \Paymill\Paymill\Helper\FastCheckoutHelper $paymillFastCheckoutHelperHelper, 
            \Paymill\Paymill\Helper\LoggingHelper $paymillLoggingHelperHelper, 
            \Paymill\Paymill\Helper\OptionHelper $paymillOptionHelperHelper, 
            \Paymill\Paymill\Helper\PaymentHelper $paymillPaymentHelperHelper, 
            \Paymill\Paymill\Helper\CustomerHelper $paymillCustomerHelperHelper, 
            \Paymill\Paymill\Helper\RefundHelper $paymillRefundHelperHelper, 
            \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, 
            \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, 
            array $data = [])
    {
        parent::__construct($context, $registry, $extensionFactory, 
                $customAttributeFactory, $paymentData, $scopeConfig, $logger, 
                $storeManager, $paymillHelper, $checkoutSession, $generic, 
                $paymillFastCheckoutHelperHelper, $paymillLoggingHelperHelper, 
                $paymillOptionHelperHelper, $paymillPaymentHelperHelper, 
                $paymillCustomerHelperHelper, $paymillRefundHelperHelper, 
                $resource, $resourceCollection, $data);
    }

    public function getPaymentEntryElv ($code)
    {
        $data = $this->getPaymentData($code);
        $fastCheckoutData = array(
                null,
                null
        );
        if (isset($data['iban'])) {
            $fastCheckoutData[0] = $data['iban'];
            $fastCheckoutData[1] = $data['bic'];
        } elseif (isset($data['account'])) {
            $fastCheckoutData[0] = $data['account'];
            $fastCheckoutData[1] = $data['code'];
        }
        return $fastCheckoutData;
    }
    
}
