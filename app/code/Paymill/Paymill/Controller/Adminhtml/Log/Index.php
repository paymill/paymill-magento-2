<?php
namespace Paymill\Paymill\Controller\Adminhtml\Log;

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
     * Initialize logs view
     *
     * @return \Paymill\Paymill\Controller\Adminhtml\Log
     */
    protected function _initAction ()
    {
        $resultPage = $this->paymillLogFactory->create();
        $resultPage->setActiveMenu('Paymill_Paymill::paymill_log');
        // $resultPage->setActiveMenu('Paymill_Paymill::adminhtml_log');
        return $resultPage;
    }

    public function execute ()
    {
        $resultPage = $this->_initAction();
        return $resultPage;
    }

    /*
     * Check permission via ACL resource
     */
    protected function _isAllowed ()
    {
        return $this->_authorization->isAllowed('Paymill_Paymill::Index');
    }
    
}
