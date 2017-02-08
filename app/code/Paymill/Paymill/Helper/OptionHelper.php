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
 * The Option Helper contains methods dealing with reading out backend options.
 */
class OptionHelper extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct (\Magento\Framework\App\Helper\Context $context, 
            \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Returns the Public Key from the Backend as a string
     *
     * @return String
     */
    public function getPublicKey ()
    {
        return trim($this->_getGeneralOption("public_key"));
    }

    /**
     * Returns the Private Key from the Backend as a string
     *
     * @return String
     */
    public function getPrivateKey ()
    {
        return trim($this->_getGeneralOption("private_key"));
    }

    /**
     * Returns the state of the "Logging" Switch from the Backend as a Boolean
     *
     * @return Boolean
     */
    public function isLogging ()
    {
        return $this->_getGeneralOption("logging_active");
    }

    /**
     * Returns the state of the "FastCheckout" Switch from the Backend as a
     * Boolean
     *
     * @return Boolean
     */
    public function isFastCheckoutEnabled ()
    {
        return $this->_getGeneralOption("fc_active");
    }

    /**
     * Returns the state of the "Debug" Switch from the Backend as a Boolean
     *
     * @return Boolean
     */
    public function isInDebugMode ()
    {
        return $this->_getGeneralOption("debugging_active");
    }

    /**
     * Returns the state of the "Show Labels" Switch from the Backend as a
     * Boolean
     *
     * @return Boolean
     */
    public function isShowingLabels ()
    {
        return $this->_getGeneralOption("show_label");
    }

    /**
     * Is base currency in use
     *
     * @return boolean
     */
    public function isBaseCurrency ()
    {
        return $this->_getGeneralOption("base_currency");
    }

    /**
     * Is base currency in use
     *
     * @return boolean
     */
    public function getCheckoutDesc ($choice)
    {
        return $this->_getBackendOption($choice, "checkout_desc");
    }

    /**
     * Return token selector
     *
     * @return string
     */
    public function getTokenSelector ()
    {
        return $this->_getGeneralOption("token_creation_identifier_id");
    }

    /**
     * Returns the value of the given backend option.
     * <p align = "center">Needs the $_storeId to be set to work properly</p>
     *
     * @param String $choice
     *            Name of the desired category as a string
     * @param String $optionName
     *            Name of the desired option as a string
     * @return mixed Value of the Backend Option
     * @throws \Exception "No Store Id has been set."
     */
    private function _getBackendOption ($choice, $optionName)
    {
        $value = $this->scopeConfig->getValue(
                'payment/' . $choice . '/' . $optionName, 
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE, 
                $this->storeManager->getStore()
                    ->getStoreId());
        
        return $value;
    }

    /**
     * Returns the Value of the general Option with the given name.
     * <p align = "center">Needs the $_storeId to be set to work properly</p>
     *
     * @param String $optionName            
     * @return mixed Value
     */
    private function _getGeneralOption ($optionName)
    {
        return $this->_getBackendOption("paymill", $optionName);
    }

    /**
     * Returns the state of the "preAuth" Switch from the Backend as a Boolean
     *
     * @return boolean
     */
    public function isPreAuthorizing ()
    {
        return $this->_getBackendOption("paymill_creditcard", "preAuth_active");
    }

    /**
     * Returns the value of the "prenotification" config from the Backend as a
     * string
     *
     * @return string
     */
    public function getPrenotificationDays ()
    {
        return $this->_getBackendOption("paymill_directdebit", 
                "prenotification");
    }

    /**
     * Returns the value of the "Payment Form" config from the Backend as a
     * string
     *
     * @return string
     */
    public function getPci ()
    {
        return $this->_getBackendOption("paymill_creditcard", "pci");
    }

    /**
     * Returns the value of the "specificcreditcard" config from the Backend as
     * a
     * string
     *
     * @return boolean
     */
    public function getSpecificCreditcard ()
    {
        return $this->_getBackendOption("paymill_creditcard", 
                "specificcreditcard");
    }

    /**
     * Returns the value of the "showspecificcreditcard" config from the Backend
     * as a
     * string
     *
     * @return boolean
     */
    public function showspecificcreditcard ()
    {
        return $this->_getBackendOption("paymill_creditcard", 
                "showspecificcreditcard");
    }
}
