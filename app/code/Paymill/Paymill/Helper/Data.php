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
 * The Data Helper contains methods dealing with shopiformation.
 * Examples for this might be f.Ex backend option states or pathes.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     *
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $_moduleList;

    /**
     *
     * @var \Paymill\Paymill\Helper\OptionHelper
     */
    protected $paymillOptionHelperHelper;

    public function __construct (\Magento\Framework\App\Helper\Context $context, 
            \Paymill\Paymill\Helper\OptionHelper $paymillOptionHelperHelper, 
            \Magento\Framework\Module\ModuleListInterface $moduleList)
    {
        $this->_moduleList = $moduleList;
        $this->paymillOptionHelperHelper = $paymillOptionHelperHelper;
        parent::__construct($context);
    }

    /**
     * Returns the API Url
     * 
     * @return string
     */
    public function getApiUrl ()
    {
        return "https://api.paymill.com/v2/";
    }

    /**
     * Returns the version of the plugin as a string
     * 
     * @return String Version
     */
    public function getVersion ()
    {
        $moduleCode = 'Paymill_Paymill';
        $moduleInfo = $this->_moduleList->getOne($moduleCode);
        return $moduleInfo['setup_version'];
    }

    /**
     * Returns the Source string passt to every transaction
     * 
     * @return String Source
     */
    public function getSourceString ()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productMetadata = $objectManager->get(
                'Magento\Framework\App\ProductMetadataInterface');
        $version = $productMetadata->getVersion();
        return $this->getVersion() . "_Magento_" . $version;
    }

    /**
     * Validates the private key value by comparing it to an empty string
     * 
     * @return boolean
     */
    public function isPrivateKeySet ()
    {
        return $this->paymillOptionHelperHelper->getPrivateKey() !== "";
    }

    /**
     * Validates the public key value by comparing it to an empty string
     * 
     * @return boolean
     */
    public function isPublicKeySet ()
    {
        return $this->paymillOptionHelperHelper->getPublicKey() !== "";
    }
    
}
