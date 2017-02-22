<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Paymill\Paymill\Controller\Adminhtml\Log;

class MassDelete extends \Magento\Backend\App\Action
{

    /**
     *
     * @var \Paymill\Paymill\Model\LogFactory
     */
    protected $paymillLogFactory = false;

    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct (\Magento\Backend\App\Action\Context $context, 
            \Magento\Framework\Registry $registry, 
            \Magento\Framework\View\Result\PageFactory $paymillLogFactory)
    {
        $this->registry = $registry;
        $this->paymillLogFactory = $paymillLogFactory;
        parent::__construct($context);
    }

    /**
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute ()
    {
        $logIds = $this->getRequest()->getParam('log_id');
        if (! is_array($logIds)) {
            $this->messageManager->addError(
                    __('paymill_error_text_no_entry_selected'));
        } else {
            try {
                foreach ($logIds as $logId) {
                    $this->_objectManager->create(
                            \Paymill\Paymill\Model\Log::class)
                        ->load($logId)
                        ->delete();
                    // $this->paymillLogFactory->create()->load($logId)->delete();
                }
                $this->messageManager->addSuccess(
                        __("paymill_log_action_success"));
            } catch (\Paymill\Paymill\Services\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
}
