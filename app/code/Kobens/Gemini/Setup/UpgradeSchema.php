<?php

namespace Kobens\Core\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (\version_compare($context->getVersion(), '0.0.2', '<')) {
            $conn = $setup->getConnection();
            $table = $conn
                ->newTable($setup->getTable(\Kobens\Gemini\Model\ResourceModel\Position\BTC\USD::MAIN_TABLE))
                ->addColumn(
                    'order_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false],
                    'Order Id'
                )
                ->addColumn(
                    'exchange',
                    Table::TYPE_TEXT,
                    255,
                    ['default' => null, 'nullable' => false],
                    'Exchange the order was placed on'
                )
                ->addColumn(
                    'side'
                )
            ;
        }
    }

}
