<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Paymill\Paymill\Controller\Adminhtml\Log;

class View extends \Magento\Backend\App\Action
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
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create(
                \Paymill\Paymill\Model\Log::class)->load($id);
        if ($model->getId()) {
            $this->registry->register('paymill_log_entry', $model);
            $resultPage = $this->paymillLogFactory->create();
            $resultPage->setActiveMenu('Paymill_Paymill::paymill_log');
            $this->_addContent(
                    $resultPage->getLayout()
                        ->createBlock(
                            'Paymill\Paymill\Block\Adminhtml\Log\View'));
            // $resultPage->renderLayout();
            return $resultPage;
        } else {
            $this->messageManager->addError(__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
    
}
