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
namespace Paymill\Paymill\Model\ResourceModel\Log;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    public function __construct (
            \Magento\Framework\Data\Collection\EntityFactory $entityFactory, 
            \Psr\Log\LoggerInterface $logger, 
            \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy, 
            \Magento\Framework\Event\ManagerInterface $eventManager, 
            \Magento\Framework\DB\Adapter\AdapterInterface $connection = null, 
            \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null)
    {
        parent::__construct($entityFactory, $logger, $fetchStrategy, 
                $eventManager, $connection, $resource);
    }

    /**
     * Construct
     */
    public function _construct ()
    {
        parent::_construct();
        $this->_init('Paymill\Paymill\Model\Log', 
                'Paymill\Paymill\Model\ResourceModel\Log');
    }
    
}
