<?php
namespace Paymill\Paymill\Block\Adminhtml\Hook;

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
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * Is filter allowed
     *
     * @var boolean
     */
    protected $_isFilterAllowed = true;

    /**
     * Is sortable
     *
     * @var boolean
     */
    protected $_isSortable = true;

    /**
     *
     * @var \Paymill\Paymill\Helper\HookHelper
     */
    protected $paymillHookHelperHelper;

    /**
     *
     * @var \Magento\Framework\Data\CollectionFactory
     */
    protected $collectionFactory;

    /**
     *
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * Construct
     */
    public function __construct (
            \Magento\Backend\Block\Template\Context $context, 
            \Magento\Backend\Helper\Data $backendHelper, 
            \Paymill\Paymill\Helper\HookHelper $paymillHookHelperHelper, 
            \Magento\Framework\Data\CollectionFactory $collectionFactory, 
            \Magento\Framework\DataObjectFactory $dataObjectFactory, 
            array $data = [])
    {
        $this->collectionFactory = $collectionFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->paymillHookHelperHelper = $paymillHookHelperHelper;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct ()
    {
        parent::_construct();
        $this->setId('hook_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Is filter allowed
     */
    protected function _isFilterAllowed ()
    {
        return $this->_isFilterAllowed;
    }

    /**
     * Is sortable
     */
    protected function _isSortable ()
    {
        return $this->_isSortable;
    }

    /**
     * Retrive massaction block
     *
     * @return Mage_Adminhtml_Block_Widget_Grid_Massaction
     */
    public function getMassactionBlock ()
    {
        return $this->getChildBlock('massaction')->setErrorText(
                __('paymill_error_text_no_entry_selected'));
    }

    /**
     * Prepare Collection
     *
     * @return \Paymill\Paymill\Block\Adminhtml\Log\Grid
     */
    protected function _prepareCollection ()
    {
        $this->setCollection($this->_getHookCollection());
        return parent::_prepareCollection();
    }

    /**
     * Retrieve config data
     *
     * @return \stdClass
     */
    protected function _getHookCollection ()
    {
        $data = $this->paymillHookHelperHelper->getAllHooks();
        if ($data) {
            $collection = $this->collectionFactory->create();
            foreach ($data as $value) {
                $obj = $this->dataObjectFactory->create();
                $obj->addData(
                        array(
                                'id' => $value['id'],
                                'target' => ! array_key_exists('url', $value) ? $value['email'] : $value['url'],
                                'live' => $value['livemode'] ? 'live' : 'test',
                                'event_types' => implode(', ', 
                                        $value['event_types'])
                        ));
                
                $collection->addItem($obj);
            }
            return $collection;
        }
        
        return null;
    }

    /**
     * Prepare Columns
     *
     * @return \Paymill\Paymill\Block\Adminhtml\Log\Grid
     */
    protected function _prepareColumns ()
    {
        $this->addColumn('id', 
                array(
                        'header' => __('paymill_backend_hook_id'),
                        'index' => 'id'
                ));
        
        $this->addColumn('event_types', 
                array(
                        'header' => __('paymill_backend_hook_event_types'),
                        'index' => 'event_types'
                ));
        
        $this->addColumn('target', 
                array(
                        'header' => __('paymill_backend_hook_target'),
                        'index' => 'target'
                ));
        
        $this->addColumn('live', 
                array(
                        'header' => __('paymill_backend_hook_live'),
                        'index' => 'live'
                ));
        
        return parent::_prepareColumns();
    }

    /**
     * Prepares Massaction for deletion of Logentries
     *
     * @return \Paymill\Paymill\Block\Adminhtml\Log\Grid
     */
    protected function _prepareMassaction ()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('hook_id');
        
        $this->getMassactionBlock()->addItem('delete', 
                array(
                        'label' => __('paymill_action_delete'),
                        'url' => $this->getUrl('*/*/massDelete'),
                        'confirm' => __('paymill_dialog_confirm')
                ));
        
        return $this;
    }
}
