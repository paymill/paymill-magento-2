<?php
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
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Paymill\Paymill\Model\Log;

class Search extends \Magento\Framework\DataObject
{

    /**
     *
     * @var \Paymill\Paymill\Model\ResourceModel\Log\CollectionFactory
     */
    protected $paymillResourceModelLogCollectionFactory;

    /**
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    public function __construct (
            \Paymill\Paymill\Model\ResourceModel\Log\CollectionFactory $paymillResourceModelLogCollectionFactory, 
            \Magento\Backend\Helper\Data $backendHelper, array $data = [])
    {
        $this->paymillResourceModelLogCollectionFactory = $paymillResourceModelLogCollectionFactory;
        $this->backendHelper = $backendHelper;
        parent::__construct($data);
    }

    /**
     * Load search results
     *
     * @return \Paymill\Paymill\Model\Log\Search
     */
    public function load ()
    {
        $arr = array();
        $searchText = $this->getQuery();
        $collection = $this->paymillResourceModelLogCollectionFactory->create()
            ->addFieldToFilter(array(
                'dev_info',
                'dev_info_additional'
        ), 
                array(
                        array(
                                'like' => '%' . $searchText . '%'
                        ),
                        array(
                                'like' => '%' . $searchText . '%'
                        )
                ))
            ->load();
        
        foreach ($collection as $model) {
            $arr[] = array(
                    'id' => 'paymill/search/' . $model->getId(),
                    'type' => __('Paymill Log Entry'),
                    'name' => $model->getMerchantInfo(),
                    'description' => $model->getEntryDate(),
                    'url' => $this->backendHelper->getUrl('paymill/log/view', 
                            array(
                                    'id' => $model->getId()
                            ))
            );
        }
        
        $this->setResults($arr);
        return $this;
    }
    
}
