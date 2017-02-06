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
class MethodModelCreditcard extends \Paymill\Paymill\Model\Method\MethodModelAbstract
{

    const PAYMENT_METHOD_CREDITCARD_CODE = 'paymill_creditcard';

    /**
     * Magento method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_CREDITCARD_CODE;

    /**
     *
     * @var \Paymill\Paymill\Helper\PaymentHelper
     */
    protected $paymillPaymentHelperHelper;

    /**
     *
     * @var \Paymill\Paymill\Helper\Data
     */
    protected $paymillHelper;

    /**
     *
     * @var \Paymill\Paymill\Helper\OptionHelper
     */
    protected $paymillOptionHelperHelper;

    /**
     *
     * @var \Paymill\Paymill\Helper\LoggingHelper
     */
    protected $paymillLoggingHelperHelper;

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

    public function processInvoice ($invoice, $payment)
    {
        $data = $payment->getAdditionalInformation();
        
        if (array_key_exists('paymillPreauthId', $data) &&
                 ! empty($data['paymillPreauthId'])) {
            
            $params = array();
            $params['amount'] = (int) $this->paymillPaymentHelperHelper->getAmount(
                    $invoice);
            $params['currency'] = $this->paymillPaymentHelperHelper->getCurrency(
                    $invoice);
            $params['description'] = $this->paymillPaymentHelperHelper->getDescription(
                    $payment->getOrder());
            $params['source'] = $this->paymillHelper->getSourceString();
            
            $paymentProcessor = new \Paymill\Paymill\Services\PaymentProcessor(
                    $this->paymillOptionHelperHelper->getPrivateKey(), 
                    $this->paymillHelper->getApiUrl(), null, $params, 
                    $this->paymillLoggingHelperHelper);
            
            $paymentProcessor->setPreauthId($data['paymillPreauthId']);
            
            if (! $paymentProcessor->capture()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                        $this->paymillPaymentHelperHelper->getErrorMessage(
                                $paymentProcessor->getErrorCode()));
            }
            
            $this->paymillLoggingHelperHelper->log("Capture created", 
                    var_export($paymentProcessor->getLastResponse(), true));
            
            $payment->setAdditionalInformation('paymillTransactionId', 
                    $paymentProcessor->getTransactionId());
        }
        
        parent::processInvoice($invoice, $payment);
    }

    public function getPaymentData ($code)
    {
        $payment = parent::getPaymentData($code);
        
        $data = array();
        if (! empty($payment)) {
            $data['cc_number'] = '************' . $payment['last4'];
            $data['expire_year'] = $payment['expire_year'];
            $data['expire_month'] = $payment['expire_month'];
            $data['cvc'] = '***';
            $data['card_holder'] = $payment['card_holder'];
            $data['card_type'] = $payment['card_type'];
        }
        
        return $data;
    }
    
}
