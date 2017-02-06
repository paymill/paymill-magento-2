<?php
namespace Paymill\Paymill\Controller\Adminhtml\Hook;

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
class Index extends \Magento\Backend\App\Action
{

    /**
     *
     * @var \Paymill\Paymill\Helper\HookHelper
     */
    protected $paymillHookHelperHelper;

    public function __construct (\Magento\Backend\App\Action\Context $context, 
            \Paymill\Paymill\Helper\HookHelper $paymillHookHelperHelper)
    {
        $this->paymillHookHelperHelper = $paymillHookHelperHelper;
        parent::__construct($context);
    }

    /**
     * Initialize hooks view
     *
     * @return \Paymill\Paymill\Controller\Adminhtml\Hook
     */
    protected function _initAction ()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Paymill_Paymill::paymill_hook');
        return $this;
    }

    public function execute ()
    {
        $this->_initAction();
        $this->_view->renderLayout();
    }
    
}
