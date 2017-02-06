<?php
namespace Paymill\Paymill\Model;

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
class Fastcheckout extends \Magento\Framework\Model\AbstractModel
{

    /**
     *
     * @var \Paymill\Paymill\Model\ResourceModel\Fastcheckout\CollectionFactory
     */
    protected $paymillResourceModelFastcheckoutCollectionFactory;

    /**
     *
     * @var \Paymill\Paymill\Helper\LoggingHelper
     */
    protected $paymillLoggingHelperHelper;

    public function __construct (\Magento\Framework\Model\Context $context, 
            \Magento\Framework\Registry $registry, 
            \Paymill\Paymill\Model\ResourceModel\Fastcheckout\CollectionFactory $paymillResourceModelFastcheckoutCollectionFactory, 
            \Paymill\Paymill\Helper\LoggingHelper $paymillLoggingHelperHelper, 
            \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, 
            \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, 
            array $data = [])
    {
        $this->paymillResourceModelFastcheckoutCollectionFactory = $paymillResourceModelFastcheckoutCollectionFactory;
        $this->paymillLoggingHelperHelper = $paymillLoggingHelperHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, 
                $data);
    }

    /**
     * Construct
     */
    public function _construct ()
    {
        parent::_construct();
        $this->_init('Paymill\Paymill\Model\ResourceModel\Fastcheckout');
    }

    /**
     * Returns the paymentId matched with the userId passed as an argument.
     * If no match is found, the return value will be null.
     * 
     * @param String $userId
     *            Unique identifier of the customer
     * @param String $code
     *            PaymentMethodCode
     * @return String paymentId matched with the userId <b>can be null if no
     *         match is found</b>
     */
    public function getPaymentId ($userId, $code)
    {
        $collection = $this->paymillResourceModelFastcheckoutCollectionFactory->create();
        $collection->addFilter('user_id', $userId);
        $obj = $collection->getFirstItem();
        if ($code === "paymill_creditcard") {
            return $obj->getCcPaymentId();
        }
        
        if ($code === "paymill_directdebit") {
            return $obj->getElvPaymentId();
        }
    }

    /**
     * Saves a set of arguments (paymentMethodCode, clientId and paymentId) as a
     * match to the Id of the current user.
     * The paymentMethodCode is used to bind the Data to the correct payment
     * type.
     * 
     * @param String $paymentMethodCode
     *            $_code from the payment model
     * @param String $clientId
     *            Code returned from the PaymentProcessor used to recreate the
     *            current client object
     * @param String $paymentId
     *            Code returned from the PaymentProcessor used to recreate the
     *            current payment object
     * @return boolean Indicator of Success
     */
    public function saveFcData ($paymentMethodCode, $userId, $clientId, 
            $paymentId)
    {
        $logger = $this->paymillLoggingHelperHelper;
        $collection = $this->paymillResourceModelFastcheckoutCollectionFactory->create();
        $collection->addFilter('user_id', $userId);
        $customerExists = $collection->count();
        
        if ($customerExists == 1) {
            $obj = $collection->getFirstItem();
            
            $obj->setClientId($clientId)->save();
            
            if ($paymentMethodCode === 'paymill_creditcard') {
                $logger->log("Saving Fast Checkout Data", 
                        "Customer data already exists. Saving CC only Data.");
                $obj->setCcPaymentId($paymentId)->save();
            }
            
            if ($paymentMethodCode === 'paymill_directdebit') {
                $logger->log("Saving Fast Checkout Data", 
                        "Customer data already exists. Saving ELV only Data.");
                $obj->setElvPaymentId($paymentId)->save();
            }
            return true;
        }
        
        // Insert into db
        if ($paymentMethodCode === 'paymill_creditcard') {
            $logger->log("Saving Fast Checkout Data", 
                    "Customer data saved with CC data");
            $this->setId(null)
                ->setUserId($userId)
                ->setClientId($clientId)
                ->setCcPaymentId($paymentId)
                ->save();
            return true;
        }
        
        if ($paymentMethodCode === 'paymill_directdebit') {
            $logger->log("Saving Fast Checkout Data", 
                    "Customer data saved with ELV data");
            $this->setId(null)
                ->setUserId($userId)
                ->setClientId($clientId)
                ->setElvPaymentId($paymentId)
                ->save();
            return true;
        }
        
        return false;
    }

    /**
     * Returns a boolean describing if there is FC Data registered for the given
     * userId
     * 
     * @param String $userId            
     * @param String $code
     *            PaymentMethodCode
     * @return boolean
     */
    public function hasFcData ($userId, $code)
    {
        $collection = $this->paymillResourceModelFastcheckoutCollectionFactory->create();
        $collection->addFilter('user_id', $userId);
        
        if ($code === "paymill_creditcard") {
            $obj = $collection->getFirstItem();
            if ($obj->getCcPaymentId() != null) {
                return true;
            }
        }
        
        if ($code === "paymill_directdebit") {
            $obj = $collection->getFirstItem();
            if ($obj->getElvPaymentId() != null) {
                return true;
            }
        }
        return false;
    }
    
}
