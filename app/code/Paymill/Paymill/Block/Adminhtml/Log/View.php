<?php
namespace Paymill\Paymill\Block\Adminhtml\Log;

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
 *         
 */
class View extends \Magento\Backend\Block\Widget\Form\Container
{

    protected $registry;

    /**
     *
     * @param \Magento\Backend\Block\Widget\Context $context            
     * @param \Magento\Framework\Registry $registry            
     * @param \Magento\Sales\Model\Config $salesConfig            
     * @param \Magento\Sales\Helper\Reorder $reorderHelper            
     * @param array $data            
     */
    public function __construct (\Magento\Backend\Block\Widget\Context $context, 
            \Magento\Framework\Registry $registry, array $data = [])
    {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Construct
     */
    public function _construct ()
    {
        parent::_construct();
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_log';
        $this->_blockGroup = 'Paymill_Paymill';
        $this->_mode = 'view';
        $this->_headerText = __('Log Entry');
        $this->buttonList->remove('edit');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');
        $this->buttonList->remove('delete');
    }

    /**
     *
     * @see Mage_Adminhtml_Block_Widget_View_Container::_prepareLayout()
     */
    protected function _prepareLayout ()
    {
        $this->setChild('form', 
                $this->getLayout()
                    ->createBlock(
                        'Paymill\Paymill\Block\Adminhtml\Log\View\Form'));
        parent::_prepareLayout();
    }
}
