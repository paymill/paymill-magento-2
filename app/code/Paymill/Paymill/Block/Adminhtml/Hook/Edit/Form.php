<?php
namespace Paymill\Paymill\Block\Adminhtml\Hook\Edit;

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
     * @var \Paymill\Paymill\Model\Source\Hooks
     */
    protected $paymillSourceHooks;

    /**
     *
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $formFactory;

    public function __construct (
            \Magento\Backend\Block\Template\Context $context, 
            \Paymill\Paymill\Model\Source\Hooks $paymillSourceHooks, 
            \Magento\Framework\Data\FormFactory $formFactory, array $data = [])
    {
        $this->formFactory = $formFactory;
        $this->paymillSourceHooks = $paymillSourceHooks;
        parent::__construct($context, $data);
    }

    public function _construct ()
    {
        parent::_construct();
    }

    protected function _prepareForm ()
    {
        $form = $this->formFactory->create();
        
        $fieldset = $form->addFieldset('base_fieldset', 
                array(
                        'legend' => __('hook_data')
                ));
        
        $fieldset->addField('hook_url', 'text', 
                array(
                        'name' => 'hook_url',
                        'class' => 'required-entry',
                        'label' => __('hook_url'),
                        'title' => __('hook_url'),
                        'required' => true,
                        'value' => $this->getUrl('*/*/execute', 
                                array(
                                        '_secure' => true
                                ))
                ));
        
        $fieldset->addField('hook_types', 'multiselect', 
                array(
                        'label' => __('hook_types'),
                        'class' => 'required-entry',
                        'required' => true,
                        'name' => 'hook_types',
                        'values' => $this->paymillSourceHooks->toOptionArray(),
                        'value' => array(
                                'refund.succeeded',
                                'transaction.succeeded',
                                'chargeback.executed'
                        )
                ));
        
        $form->setAction($this->getUrl('*/*/save'));
        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');
        
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
}
