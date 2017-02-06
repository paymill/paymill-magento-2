<?php
namespace Paymill\Paymill\Setup;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

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
class InstallData implements InstallDataInterface
{

    public function install (ModuleDataSetupInterface $setup, 
            ModuleContextInterface $context)
    {
        $setup->getConnection()->query(
                "UPDATE `{$setup->getTable('quote_payment')}` SET method = 'paymill_creditcard' WHERE method = 'paymillcc';");
        $setup->getConnection()->query(
                "UPDATE `{$setup->getTable('quote_payment')}` SET method = 'paymill_directdebit' WHERE method = 'paymillelv';");
    }
}
