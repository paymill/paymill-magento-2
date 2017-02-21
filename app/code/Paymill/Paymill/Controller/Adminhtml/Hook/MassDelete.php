<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Paymill\Paymill\Controller\Adminhtml\Hook;

class MassDelete extends \Magento\Backend\App\Action
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
        $hookIds = $this->getRequest()->getParam('hook_id');
        
        if (! is_array($hookIds)) {
            $this->messageManager->addError(
                    __('paymill_error_text_no_entry_selected'));
        } else {
            try {
                foreach ($hookIds as $hookId) {
                    $this->paymillHookHelperHelper->deleteHook($hookId);
                }
                
                $this->messageManager->addSuccess(
                        __("paymill_hook_action_success"));
            } catch (\Paymill\Paymill\Services\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        
        $this->_redirect('*/*/index');
    }
    
}
