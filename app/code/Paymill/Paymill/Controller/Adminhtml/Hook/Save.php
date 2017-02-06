<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Paymill\Paymill\Controller\Adminhtml\Hook;

class Save extends \Magento\Backend\App\Action
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
        $post = $this->getRequest()->getPost();
        if (array_key_exists('hook_url', $post) &&
                 array_key_exists('hook_types', $post)) {
            $this->paymillHookHelperHelper->createHook(
                    array(
                            'url' => $post['hook_url'],
                            'event_types' => $post['hook_types']
                    ));
        }
        
        $this->_redirect('*/*/index');
    }
    
}
