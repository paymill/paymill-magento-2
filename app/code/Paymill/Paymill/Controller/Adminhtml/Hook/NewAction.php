<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Paymill\Paymill\Controller\Adminhtml\Hook;

class NewAction extends \Magento\Backend\App\Action
{

    /**
     *
     * @var \Paymill\Paymill\Model\LogFactory
     */
    protected $paymillHookHelperHelper = false;

    public function __construct (\Magento\Backend\App\Action\Context $context, 
            \Paymill\Paymill\Helper\HookHelper $paymillHookHelperHelper)
    {
        $this->paymillHookHelperHelper = $paymillHookHelperHelper;
        parent::__construct($context);
    }

    /**
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute ()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Paymill_Paymill::paymill_hook');
        $this->_addContent(
                $this->_view->getLayout()
                    ->createBlock('Paymill\Paymill\Block\Adminhtml\Hook\Edit'));
        $this->_view->renderLayout();
    }
    
}
