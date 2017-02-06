<?php
namespace Paymill\Paymill\Block\Adminhtml\Log\View;

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
class Form extends \Magento\Backend\Block\Widget\Form
{

    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct (
            \Magento\Backend\Block\Template\Context $context, 
            \Magento\Framework\Registry $registry, array $data = [])
    {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct ()
    {
        parent::_construct();
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return \Paymill\Paymill\Block\Adminhtml\Log\View\Plane
     */
    protected function _prepareForm ()
    {
        $this->setTemplate('Paymill_Paymill::log/view.phtml');
        return parent::_prepareForm();
    }

    /**
     * Returns Log Model
     *
     * @return \Paymill\Paymill\Model\Log
     */
    public function getEntry ()
    {
        return $this->registry->registry('paymill_log_entry');
    }

    /**
     * Gets the formatted Request Xml
     *
     * @return string
     */
    public function getDevInfo ()
    {
        return $this->getEntry()->getDevInfo();
    }

    /**
     * Gets the formatted Response Xml
     *
     * @return string
     */
    public function getDevInfoAdditional ()
    {
        return $this->getEntry()->getDevInfoAdditional();
    }
}
