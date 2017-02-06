<?php
namespace Paymill\Paymill\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

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
class GenerateInvoice implements ObserverInterface
{

    /**
     *
     * @var \Paymill\Paymill\Helper\LoggingHelper
     */
    protected $paymillLoggingHelperHelper;

    /**
     *
     * @var \Paymill\Paymill\Helper\PaymentHelper
     */
    protected $paymillPaymentHelperHelper;

    /**
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    protected $_order;

    public function __construct (
            \Paymill\Paymill\Helper\LoggingHelper $paymillLoggingHelperHelper, 
            \Paymill\Paymill\Helper\PaymentHelper $paymillPaymentHelperHelper, 
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, 
            \Magento\Store\Model\StoreManagerInterface $storeManager, 
            \Magento\Sales\Api\Data\OrderInterface $order)
    {
        $this->paymillLoggingHelperHelper = $paymillLoggingHelperHelper;
        $this->paymillPaymentHelperHelper = $paymillPaymentHelperHelper;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_order = $order;
    }

    /**
     * Registered for the checkout_onepage_controller_success_action event
     * Generates the invoice for the current order
     *
     * @param \Magento\Framework\Event\Observer $observer            
     */
    public function execute (EventObserver $observer)
    {
        // $order = $observer->getEvent()->getOrder();
        $orderids = $observer->getEvent()->getOrderIds();
        foreach ($orderids as $orderid) {
            $order = $this->_order->load($orderid);
            if ($order->getPayment()->getMethod() === 'paymill_creditcard') {
                $data = $order->getPayment()->getAdditionalInformation();
                
                if (array_key_exists('paymillPreauthId', $data) &&
                         ! empty($data['paymillPreauthId'])) {
                    $this->paymillLoggingHelperHelper->log("Debug", 
                            "No Invoice generated, since the transaction is flagged as preauth");
                } else {
                    $this->paymillPaymentHelperHelper->invoice($order, 
                            $data['paymillTransactionId'], 
                            $this->scopeConfig->getValue(
                                    'payment/paymill_creditcard/send_invoice_mail', 
                                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE, 
                                    $this->storeManager->getStore()
                                        ->getStoreId()));
                }
            }
        }
    }
    
}
