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
abstract class MethodModelAbstract extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Can use the Authorize method
     *
     * @var boolean
     */
    protected $_canAuthorize = true;

    /**
     * Can use the Refund method
     *
     * @var boolean
     */
    protected $_canRefund = true;

    /**
     * Can use the Refund method to refund less than the full amount
     *
     * @var boolean
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * Can use the Capture method
     *
     * @var boolean
     */
    protected $_canCapture = true;

    /**
     * Can use the partial capture method
     *
     * @var boolean
     */
    protected $_canCapturePartial = false;

    /**
     * Can this method use for checkout
     *
     * @var boolean
     */
    protected $_canUseCheckout = true;

    /**
     * Can this method use for multishipping
     *
     * @var boolean
     */
    protected $_canUseForMultishipping = false;

    /**
     * Payment Title
     *
     * @var type
     */
    protected $_methodTitle = '';

    /**
     * Magento method code
     *
     * @var string
     */
    protected $_code = 'paymill_abstract';

    /**
     * Paymill error code
     *
     * @var string
     */
    protected $_errorCode;

    /**
     * Is pre-auth
     *
     * @var boolean
     */
    protected $_preauthFlag;

    /**
     * Can use for internal payments
     *
     * @var boolean
     */
    protected $_canUseInternal = false;

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     *
     * @var \Paymill\Paymill\Helper\Data
     */
    protected $paymillHelper;

    /**
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     *
     * @var \Magento\Framework\Session\Generic
     */
    protected $generic;

    /**
     *
     * @var \Paymill\Paymill\Helper\FastCheckoutHelper
     */
    protected $paymillFastCheckoutHelperHelper;

    /**
     *
     * @var \Paymill\Paymill\Helper\LoggingHelper
     */
    protected $paymillLoggingHelperHelper;

    /**
     *
     * @var \Paymill\Paymill\Helper\OptionHelper
     */
    protected $paymillOptionHelperHelper;

    /**
     *
     * @var \Paymill\Paymill\Helper\PaymentHelper
     */
    protected $paymillPaymentHelperHelper;

    /**
     *
     * @var \Paymill\Paymill\Helper\CustomerHelper
     */
    protected $paymillCustomerHelperHelper;

    /**
     *
     * @var \Paymill\Paymill\Helper\RefundHelper
     */
    protected $paymillRefundHelperHelper;

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
                $resource, $resourceCollection, $data);
        $this->storeManager = $storeManager;
        $this->paymillHelper = $paymillHelper;
        $this->checkoutSession = $checkoutSession;
        $this->generic = $generic;
        $this->paymillFastCheckoutHelperHelper = $paymillFastCheckoutHelperHelper;
        $this->paymillLoggingHelperHelper = $paymillLoggingHelperHelper;
        $this->paymillOptionHelperHelper = $paymillOptionHelperHelper;
        $this->paymillPaymentHelperHelper = $paymillPaymentHelperHelper;
        $this->paymillCustomerHelperHelper = $paymillCustomerHelperHelper;
        $this->paymillRefundHelperHelper = $paymillRefundHelperHelper;
    }

    /**
     * Check if currency is avaible for this payment
     *
     * @param string $currencyCode            
     * @return boolean
     */
    public function canUseForCurrency ($currencyCode)
    {
        $availableCurrencies = explode(',', 
                $this->getConfigData('currency', 
                        $this->storeManager->getStore()
                            ->getId()));
        if (! in_array($currencyCode, $availableCurrencies)) {
            return false;
        }
        return true;
    }

    /**
     * Defines if the payment method is available for checkout
     *
     * @param \Magento\Quote\Model\Quote $quote            
     * @return boolean
     */
    public function isAvailable (
            \Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        $keysAreSet = $this->paymillHelper->isPublicKeySet() &&
                 $this->paymillHelper->isPrivateKeySet();
        return parent::isAvailable($quote) && $keysAreSet;
    }

    /**
     * Return Quote or Order Object depending on the type of the payment info
     *
     * @return \Magento\Sales\Model\Order | Mage_Sales_Model_Order_Quote
     */
    public function getOrder ()
    {
        $paymentInfo = $this->getInfoInstance();
        
        if ($paymentInfo instanceof \Magento\Sales\Model\Order\Payment) {
            return $paymentInfo->getOrder();
        }
        
        return $paymentInfo->getQuote();
    }

    /**
     * Get the title of every payment option with payment fee if available
     *
     * @return string
     */
    public function getTitle ()
    {
        $quote = $this->checkoutSession->getQuote();
        $storeId = $quote ? $quote->getStoreId() : null;
        
        return __($this->getConfigData('title', $storeId));
    }

    /**
     * Assing data to information model object for fast checkout
     * Saves Session Variables.
     *
     * @param mixed $data            
     */
    public function assignData (\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);
        if (is_array($data)) {
            $post = $data;
        } else {
            $post = $data->getData('additional_data');
        }
        if (array_key_exists('paymill-payment-token-' . $this->_getShortCode(), 
                $post) &&
                 ! empty(
                        $post['paymill-payment-token-' . $this->_getShortCode()])) {
            // Save Data into session
            $this->generic->setToken(
                    $post['paymill-payment-token-' . $this->_getShortCode()]);
            $this->generic->setPaymentCode($this->getCode());
        } else {
            if ($this->paymillFastCheckoutHelperHelper->hasData($this->_code)) {
                $this->generic->setToken('dummyToken');
            }
        }
        
        // Finish as usual
        return $this;
    }

    /**
     * Gets Excecuted when the checkout button is pressed.
     *
     * @param \Magento\Framework\DataObject $payment            
     * @param float $amount            
     * @throws \Exception
     */
    public function authorize (\Magento\Payment\Model\InfoInterface $payment, 
            $amount)
    {
        $token = $this->generic->getToken();
        if (empty($token)) {
            $this->paymillLoggingHelperHelper->log("No token found.");
            throw new \Magento\Framework\Exception\LocalizedException(
                    "There was an error processing your payment.");
        }
        if ($this->paymillOptionHelperHelper->isPreAuthorizing() &&
                 $this->_code === "paymill_creditcard") {
            $this->paymillLoggingHelperHelper->log(
                    "Starting payment process as preAuth");
            $this->_preauthFlag = true;
        } else {
            $this->paymillLoggingHelperHelper->log(
                    "Starting payment process as debit");
            $this->_preauthFlag = false;
        }
        $success = $this->payment($payment, $amount);
        if (! $success) {
            $this->paymillLoggingHelperHelper->log(
                    $this->paymillPaymentHelperHelper->getErrorMessage(
                            $this->_errorCode));
            $this->checkoutSession->setGotoSection('payment');
            throw new \Magento\Framework\Exception\LocalizedException(
                    $this->paymillPaymentHelperHelper->getErrorMessage(
                            $this->_errorCode));
        }
        // Finish as usual
        return parent::authorize($payment, $amount);
    }

    /**
     * Processing the payment or preauth
     *
     * @return booelan Indicator of success
     */
    public function payment (\Magento\Framework\DataObject $payment, $amount)
    {
        // Gathering data from session
        $token = $this->generic->getToken();
        // Create Payment Processor
        $paymentHelper = $this->paymillPaymentHelperHelper;
        $fcHelper = $this->paymillFastCheckoutHelperHelper;
        $paymentProcessor = $paymentHelper->createPaymentProcessor(
                $this->getCode(), $token);
        
        // Always load client if email doesn't change
        $clientId = $fcHelper->getClientId();
        if (isset($clientId) && ! is_null(
                $this->paymillCustomerHelperHelper->getClientData($clientId))) {
            $paymentProcessor->setClientId($clientId);
        }
        
        // Loading Fast Checkout Data (if enabled and given)
        if ($fcHelper->hasData($this->_code) && $token === 'dummyToken') {
            $paymentId = $fcHelper->getPaymentId($this->_code);
            if (isset($paymentId) &&
                     ! is_null($fcHelper->getPaymentData($this->_code))) {
                $paymentProcessor->setPaymentId($paymentId);
            }
        }
        
        $success = $paymentProcessor->processPayment(! $this->_preauthFlag);
        
        $this->_existingClientHandling($clientId);
        
        if ($success) {
            if ($this->_preauthFlag) {
                $payment->setAdditionalInformation('paymillPreauthId', 
                        $paymentProcessor->getPreauthId());
            } else {
                $payment->setAdditionalInformation('paymillTransactionId', 
                        $paymentProcessor->getTransactionId());
            }
            
            // Allways update the client
            $clientId = $paymentProcessor->getClientId();
            $fcHelper->saveData($this->_code, $clientId);
            
            // Save payment data for FastCheckout (if enabled)
            if ($fcHelper->isFastCheckoutEnabled()) { // Fast checkout enabled
                $paymentId = $paymentProcessor->getPaymentId();
                $fcHelper->saveData($this->_code, $clientId, $paymentId);
            }
            return true;
        }
        $this->_errorCode = $paymentProcessor->getErrorCode();
        return false;
    }

    /**
     * Handle paymill client update if exist
     *
     * @param string $clientId            
     */
    private function _existingClientHandling ($clientId)
    {
        if (! empty($clientId)) {
            $clients = new \Paymill\Paymill\Services\Clients(
                    trim($this->paymillOptionHelperHelper->getPrivateKey()), 
                    $this->paymillHelper->getApiUrl());
            $quote = $this->checkoutSession->getQuote();
            $client = $clients->getOne($clientId);
            if ($this->paymillCustomerHelperHelper->getCustomerEmail($quote) !==
                     $client['email']) {
                $clients->update(
                        array(
                                'id' => $clientId,
                                'email' => $this->paymillCustomerHelperHelper->getCustomerEmail(
                                        $quote)
                        ));
            }
        }
    }

    /**
     * Return paymill short code
     *
     * @return string
     */
    protected function _getShortCode ()
    {
        $methods = array(
                'paymill_creditcard' => 'cc',
                'paymill_directdebit' => 'elv'
        );
        
        return $methods[$this->_code];
    }

    public function processCreditmemo ($creditmemo, $payment)
    {
        parent::processCreditmemo($creditmemo, $payment);
        $order = $payment->getOrder();
        if ($order->getPayment()->getMethod() === 'paymill_creditcard' ||
                 $order->getPayment()->getMethod() === 'paymill_directdebit') {
            if (! $this->paymillRefundHelperHelper->createRefund($creditmemo, 
                    $payment)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                        'Refund failed.');
            }
        }
    }

    /**
     * Set invoice transaction id
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice            
     * @param type $payment            
     */
    public function processInvoice ($invoice, $payment)
    {
        parent::processInvoice($invoice, $payment);
        $invoice->setTransactionId(
                $payment->getAdditionalInformation('paymillTransactionId'));
    }

    public function getPaymentData ($code)
    {
        return $this->paymillFastCheckoutHelperHelper->getPaymentData($code);
    }

    public function getPaymentEntry ($code, $key)
    {
        $data = $this->getPaymentData($code);
        return array_key_exists($key, $data) ? $data[$key] : null;
    }

    public function isPaymentDataAvailable ($code)
    {
        return $this->paymillFastCheckoutHelperHelper->hasData($code);
    }

    /**
     * Returns a boolean if checkout is fastcheckout or not
     *
     * @param String $code
     *            payment code
     * @return boolean
     */
    public function isFastCheckout ($code)
    {
        $paymentData = $this->paymillFastCheckoutHelperHelper->getPaymentData(
                $code);
        return empty($paymentData) ? 'false' : 'true';
    }

    public function isInDebugMode ()
    {
        return $this->paymillOptionHelperHelper->isInDebugMode();
    }

    public function getTokenSelector ()
    {
        return $this->paymillOptionHelperHelper->getTokenSelector();
    }

    public function getPublicKey ()
    {
        return $this->paymillOptionHelperHelper->getPublicKey();
    }

    public function getCheckoutDesc ($method)
    {
        return $this->paymillOptionHelperHelper->getCheckoutDesc($method);
    }

    public function getPci ()
    {
        return $this->paymillOptionHelperHelper->getPci();
    }

    public function getCurrency ()
    {
        return $this->paymillPaymentHelperHelper->getCurrency(
                $this->checkoutSession->getQuote());
    }

    public function getCustomerEmail ()
    {
        return $this->paymillCustomerHelperHelper->getCustomerEmail(
                $this->checkoutSession->getQuote());
    }
}
