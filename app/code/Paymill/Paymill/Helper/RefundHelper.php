<?php
namespace Paymill\Paymill\Helper;

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

/**
 * The Refund Helper contains methods dealing with refund processes.
 */
class RefundHelper extends \Magento\Framework\App\Helper\AbstractHelper
{

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
     * @var \Paymill\Paymill\Helper\Data
     */
    protected $paymillHelper;

    /**
     *
     * @var \Paymill\Paymill\Helper\PaymentHelper
     */
    protected $paymillPaymentHelperHelper;

    /**
     *
     * @var \Magento\Framework\DB\TransactionFactory
     */
    protected $transactionFactory;

    public function __construct (\Magento\Framework\App\Helper\Context $context, 
            \Paymill\Paymill\Helper\LoggingHelper $paymillLoggingHelperHelper, 
            \Paymill\Paymill\Helper\OptionHelper $paymillOptionHelperHelper, 
            \Paymill\Paymill\Helper\Data $paymillHelper, 
            \Paymill\Paymill\Helper\PaymentHelper $paymillPaymentHelperHelper, 
            \Magento\Framework\DB\TransactionFactory $transactionFactory)
    {
        $this->paymillLoggingHelperHelper = $paymillLoggingHelperHelper;
        $this->paymillOptionHelperHelper = $paymillOptionHelperHelper;
        $this->paymillHelper = $paymillHelper;
        $this->paymillPaymentHelperHelper = $paymillPaymentHelperHelper;
        $this->transactionFactory = $transactionFactory;
        parent::__construct($context);
    }

    /**
     * Validates the result of the refund
     * 
     * @param mixed $refund            
     * @return boolean
     */
    private function validateRefund ($refund)
    {
        // Logs errorfeedback in case of any other response than ok
        if (isset($refund['data']['response_code']) &&
                 $refund['data']['response_code'] !== 20000) {
            $this->paymillLoggingHelperHelper->log(
                    "An Error occured: " . $refund['data']['response_code'], 
                    var_export($refund, true));
            return false;
        }
        
        // Logs feedback in case of an unset id
        if (! isset($refund['id']) && ! isset($refund['data']['id'])) {
            $this->paymillLoggingHelperHelper->log("No Refund created.", 
                    var_export($refund, true));
            return false;
        } else { // Logs success feedback for debugging purposes
            $this->paymillLoggingHelperHelper->log("Refund created.", 
                    $refund['id'], var_export($refund, true));
        }
        
        return true;
    }

    /**
     * Creates a refund from the ordernumber passed as an argument
     * 
     * @param Mage_Sales_Model_Order_Refund $creditmemo            
     * @return boolean Indicator of success
     */
    public function createRefund ($creditmemo, $payment)
    {
        // Gather Data
        try {
            $refundsObject = new \Paymill\Paymill\Services\Refunds(
                    $this->paymillOptionHelperHelper->getPrivateKey(), 
                    $this->paymillHelper->getApiUrl());
        } catch (\Paymill\Paymill\Services\Exception $ex) {
            $this->paymillLoggingHelperHelper->log(
                    "No Refund created due to illegal parameters.", 
                    $ex->getMessage());
            return false;
        }
        
        // Create Refund
        $params = array(
                'transactionId' => $payment->getAdditionalInformation(
                        'paymillTransactionId'),
                'source' => $this->paymillHelper->getSourceString(),
                'params' => array(
                        'amount' => (int) $this->paymillPaymentHelperHelper->getAmount(
                                $creditmemo)
                )
        );
        
        $this->paymillLoggingHelperHelper->log("Try to refund.", 
                var_export($params, true));
        
        try {
            $refund = $refundsObject->create($params);
        } catch (\Paymill\Paymill\Services\Exception $ex) {
            $this->paymillLoggingHelperHelper->log("No Refund created.", 
                    $ex->getMessage(), var_export($params, true));
            return false;
        }
        
        // Validate Refund and return feedback
        return $this->validateRefund($refund);
    }

    public function creditmemo (\Magento\Sales\Model\Order $order, $refundId)
    {
        if ($order->canCreditmemo()) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $service = $objectManager->get('sales/service_order', $order);
            $creditmemo = $service->prepareCreditmemo();
            
            $creditmemo->setOfflineRequested(true);
            
            $creditmemo->register();
            
            $this->transactionFactory->create()
                ->addObject($creditmemo)
                ->addObject($creditmemo->getOrder())
                ->save();
            
            $creditmemo->setTransactionId($refundId);
            
            $creditmemo->save();
        }
    }
    
}
