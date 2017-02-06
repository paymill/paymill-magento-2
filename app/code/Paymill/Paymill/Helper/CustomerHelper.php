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
 * The Customer Helper contains methods dealing customer information.
 */
class CustomerHelper extends \Magento\Framework\App\Helper\AbstractHelper
{

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
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    public function __construct (\Magento\Framework\App\Helper\Context $context, 
            \Paymill\Paymill\Helper\OptionHelper $paymillOptionHelperHelper, 
            \Paymill\Paymill\Helper\Data $paymillHelper, 
            \Magento\Customer\Model\Session $customerSession)
    {
        $this->paymillOptionHelperHelper = $paymillOptionHelperHelper;
        $this->paymillHelper = $paymillHelper;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Returns the current customers full name
     * 
     * @param \Magento\Quote\Model\Quote|\Magento\Sales\Model\Order $object            
     * @return string the customers full name
     */
    public function getCustomerName ($object)
    {
        $custFirstName = $object->getBillingAddress()->getFirstname();
        $custLastName = $object->getBillingAddress()->getLastname();
        $custFullName = $custFirstName . " " . $custLastName;
        return $custFullName;
    }

    /**
     * Returns the current customers email adress.
     * 
     * @param \Magento\Quote\Model\Quote|\Magento\Sales\Model\Order $object            
     * @return string the customers email adress
     */
    public function getCustomerEmail ($object)
    {
        $email = $object->getCustomerEmail();
        
        if (empty($email)) {
            $email = $object->getBillingAddress()->getEmail();
        }
        
        return $email;
    }

    /**
     * Returns the Id of the user currently logged in.
     * Returns null if there is no logged in user.
     * 
     * @return String userId
     */
    public function getUserId ()
    {
        $result = null;
        if ($this->customerSession->isLoggedIn()) {
            $result = $this->customerSession->getId();
        }
        
        return $result;
    }

    /**
     * Return paymill client data
     * 
     * @return array
     */
    public function getClientData ($clientId)
    {
        $clients = new \Paymill\Paymill\Services\Clients(
                $this->paymillOptionHelperHelper->getPrivateKey(), 
                $this->paymillHelper->getApiUrl());
        
        $client = null;
        if (! empty($clientId)) {
            $client = $clients->getOne($clientId);
            if (! array_key_exists('email', $client)) {
                $client = null;
            }
        }
        
        return $client;
    }
    
}
