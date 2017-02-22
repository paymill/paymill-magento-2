<?php

namespace Paymill\Paymill\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

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
class InstallSchema implements InstallSchemaInterface
{

    public function install (SchemaSetupInterface $setup, 
            ModuleContextInterface $context)
    {
        $installer = $setup;
        
        $installer->startSetup();
        
        /**
         * Create table 'paymill_log'
         */
        $tableName_paymill_log = $installer->getTable('paymill_log');
        
        if (! $installer->getConnection()->isTableExists($tableName_paymill_log)) {
            
            $table_paymill_log = $installer->getConnection()
                ->newTable($tableName_paymill_log)
                ->addColumn('id', Table::TYPE_INTEGER, null, 
                    [
                            'identity' => true,
                            'nullable' => false,
                            'primary' => true
                    ], 'ID')
                ->addColumn('entry_date', Table::TYPE_TIMESTAMP, null, 
                    [
                            'nullable' => false,
                            'default' => Table::TIMESTAMP_INIT
                    ], 'Entry date')
                ->addColumn('version', Table::TYPE_TEXT, 25, 
                    [
                            'nullable' => false
                    ], 'Version')
                ->addColumn('merchant_info', Table::TYPE_TEXT, 250, [], 
                    'Merchant info')
                ->addColumn('dev_info', Table::TYPE_TEXT, '2M', 
                    [
                            'default' => null
                    ], 'Dev info')
                ->addColumn('dev_info_additional', Table::TYPE_TEXT, '2M', 
                    [
                            'default' => null
                    ], 'Dev info additional');
            
            $installer->getConnection()->createTable($table_paymill_log);
        }
        
        $tableName_paymill_fastCheckout = $installer->getTable(
                'paymill_fastCheckout');
        
        if (! $installer->getConnection()->isTableExists(
                $tableName_paymill_fastCheckout)) {
            
            $table_paymill_fastCheckout = $installer->getConnection()
                ->newTable($tableName_paymill_fastCheckout)
                ->addColumn('id', Table::TYPE_INTEGER, null, 
                    [
                            'identity' => true,
                            'nullable' => false,
                            'primary' => true
                    ], 'ID')
                ->addColumn('user_id', Table::TYPE_TEXT, 250, 
                    [
                            'nullable' => false,
                            'unique' => true
                    ], 'User id')
                ->addColumn('client_id', Table::TYPE_TEXT, 250, 
                    [
                            'nullable' => false
                    ], 'Client id')
                ->addColumn('cc_payment_id', Table::TYPE_TEXT, 250, [], 
                    'Cc payment id')
                ->addColumn('elv_payment_id', Table::TYPE_TEXT, 250, [], 
                    'Elv payment id');
            
            $installer->getConnection()->createTable(
                    $table_paymill_fastCheckout);
        }
        
        $installer->endSetup();
    }
    
}
