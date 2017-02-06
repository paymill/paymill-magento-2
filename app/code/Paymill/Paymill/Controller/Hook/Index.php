<?php
namespace Paymill\Paymill\Controller\Hook;

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
class Index extends \Magento\Framework\App\Action\Action
{

    private $_eventType = '';

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

    /**
     *
     * @var \Paymill\Paymill\Helper\RefundHelper
     */
    protected $paymillRefundHelperHelper;

    /**
     *
     * @var \Paymill\Paymill\Helper\OptionHelper
     */
    protected $paymillOptionHelperHelper;

    /**
     *
     * @var \Paymill\Paymill\Helper\Data
     */
    protected $paymillHelper;

    /**
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $salesOrderFactory;

    public function __construct (\Magento\Framework\App\Action\Context $context, 
            \Paymill\Paymill\Helper\PaymentHelper $paymillPaymentHelperHelper, 
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, 
            \Magento\Store\Model\StoreManagerInterface $storeManager, 
            \Paymill\Paymill\Helper\RefundHelper $paymillRefundHelperHelper, 
            \Paymill\Paymill\Helper\OptionHelper $paymillOptionHelperHelper, 
            \Paymill\Paymill\Helper\Data $paymillHelper, 
            \Magento\Sales\Model\OrderFactory $salesOrderFactory)
    {
        $this->paymillPaymentHelperHelper = $paymillPaymentHelperHelper;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->paymillRefundHelperHelper = $paymillRefundHelperHelper;
        $this->paymillOptionHelperHelper = $paymillOptionHelperHelper;
        $this->paymillHelper = $paymillHelper;
        $this->salesOrderFactory = $salesOrderFactory;
        parent::__construct($context);
    }

    public function execute ()
    {
        $data = json_decode($this->getRequest()->getRawBody(), true);
        if ($data && $this->_validateRequest($data)) {
            $eventResource = $data['event']['event_resource'];
            $this->_eventType = $data['event']['event_type'];
            switch ($this->_eventType) {
                case 'transaction.succeeded':
                    $this->_transactionSucceededAction($eventResource);
                    break;
                case 'refund.succeeded':
                    $this->_refundSucceededAction($eventResource);
                    break;
                case 'chargeback.executed':
                    $this->_chargebackExecutedAction($eventResource);
                    break;
            }
        }
    }

    private function _transactionSucceededAction (array $data)
    {
        $order = $this->getOrder($data);
        
        if (((int) $this->paymillPaymentHelperHelper->getAmount($order) ===
                 (int) $data['amount']) && $this->scopeConfig->getValue(
                        'payment/' .
                         $order->getPayment()
                            ->getMethodInstance()
                            ->getCode() . '/hook_create_invoice_active', 
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE, 
                        $this->storeManager->getStore()
                            ->getStoreId())) {
            $this->paymillPaymentHelperHelper->invoice($order, $data['id'], 
                    $this->scopeConfig->getValue(
                            'payment/' .
                             $order->getPayment()
                                ->getMethodInstance()
                                ->getCode() . '/send_hook_invoice_mail', 
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, 
                            $this->storeManager->getStore()
                                ->getStoreId()));
        }
        
        $order->addStatusHistoryComment(
                $this->_eventType . ' event executed. ' . $data['amount'] / 100 .
                         ' ' . $data['currency'] . ' captured.')->save();
    }

    private function _refundSucceededAction (array $data)
    {
        $order = $this->getOrder($data['transaction']);
        
        if ((int) $this->paymillPaymentHelperHelper->getAmount($order) ===
                 (int) $data['amount']) {
            $this->paymillRefundHelperHelper->creditmemo($order, $data['id']);
        }
        
        $order->addStatusHistoryComment(
                $this->_eventType . ' event executed. ' . $data['amount'] / 100 .
                         ' ' . $data['transaction']['currency'] . ' refunded.')->save();
    }

    private function _chargebackExecutedAction (array $data)
    {
        $order = $this->getOrder($data['transaction']);
        $this->paymillRefundHelperHelper->creditmemo($order, $data['id']);
        
        $order->addStatusHistoryComment(
                $this->_eventType . ' event executed. ' . $data['amount'] / 100 .
                         ' ' . $data['transaction']['currency'] .
                         ' chargeback received.')->save();
    }

    private function _validateRequest ($data)
    {
        $valid = false;
        if (! is_null($data) && isset($data['event']) &&
                 isset($data['event']['event_resource'])) {
            
            $transactionId = $data['event']['event_resource']['id'];
            
            if (substr($transactionId, 0, 4) !== 'tran') {
                $transactionId = $data['event']['event_resource']['transaction']['id'];
            }
            
            $transactionObject = new \Paymill\Paymill\Services\Transactions(
                    trim($this->paymillOptionHelperHelper->getPrivateKey()), 
                    $this->paymillHelper->getApiUrl());
            
            $transaction = $transactionObject->getOne($transactionId);
            
            if (isset($transaction['id']) &&
                     ($transaction['id'] === $transactionId)) {
                $valid = true;
            }
        }
        
        return $valid;
    }

    private function getOrder (array $data)
    {
        $description = '';
        
        if (empty($description) && array_key_exists('preauthorization', $data)) {
            $description = $data['preauthorization']['description'];
        }
        
        if (empty($description) && array_key_exists('transaction', $data)) {
            $description = $data['transaction']['description'];
        }
        
        if (empty($description) && array_key_exists('description', $data)) {
            $description = $data['description'];
        }
        
        return $this->salesOrderFactory->create()->loadByIncrementId(
                substr($description, 0, 9));
    }
    
}
