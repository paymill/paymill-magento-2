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
class Log extends \Magento\Framework\Model\AbstractModel
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

    public function __construct (\Magento\Framework\Model\Context $context, 
            \Magento\Framework\Registry $registry, 
            \Paymill\Paymill\Helper\OptionHelper $paymillOptionHelperHelper, 
            \Paymill\Paymill\Helper\Data $paymillHelper, 
            \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, 
            \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, 
            array $data = [])
    {
        $this->paymillOptionHelperHelper = $paymillOptionHelperHelper;
        $this->paymillHelper = $paymillHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, 
                $data);
    }

    /**
     * Construct
     */
    public function _construct ()
    {
        parent::_construct();
        $this->_init('Paymill\Paymill\Model\ResourceModel\Log');
    }

    /**
     * Inserts the arguments into the db log
     * 
     * @param String $merchantInfo            
     * @param String $devInfo            
     * @param String $devInfoAdditional            
     */
    public function log ($merchantInfo, $devInfo, $devInfoAdditional = null)
    {
        if ($this->paymillOptionHelperHelper->isLogging()) {
            $this->setId(null)
                ->setEntryDate(null)
                ->setVersion($this->paymillHelper->getVersion())
                ->setMerchantInfo($merchantInfo)
                ->setDevInfo($devInfo)
                ->setDevInfoAdditional($devInfoAdditional)
                ->save();
        }
    }
    
}
