<?php

namespace Kobens\Core\Setup;

use Kobens\Gemini\Api\StrategyInterface;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    /**
     * @var \Kobens\Gemini\Model\ResourceModel\Strategy
     */
    protected $strategyResource;

    /**
     * Constructor
     * 
     * @param \Kobens\Gemini\Model\ResourceModel\Strategy $strategyResource
     */
    public function __construct(
        \Kobens\Gemini\Model\ResourceModel\Strategy $strategyResource
    ) {
        $this->strategyResource = $strategyResource;        
    }
    
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (\version_compare($context->getVersion(), '0.0.2', '<')) {
            $conn = $setup->getConnection();
            
            $table = $conn
                ->newTable(StrategyInterface::MAIN_TABLE)
                ->addColumn(
                    StrategyInterface::STRATEGY_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false],
                    'Strategy ID'
                )
                ->addColumn(
                    StrategyInterface::PAIR_SYMBOL,
                    Table::TYPE_TEXT,
                    10,
                    ['default' => null, 'nullable' => false],
                    'Pair Symbol'
                )
                ->addColumn(
                    StrategyInterface::OPEN_PRICE,
                    Table::TYPE_TEXT,
                    255,
                    ['default' => null, 'nullable' => false],
                    'Open Price'
                )
                ->addColumn(
                    StrategyInterface::OPEN_AMOUNT,
                    Table::TYPE_TEXT,
                    255,
                    ['default' => null, 'nullable' => false],
                    'Open Amount'
                )
                ->addColumn(
                    StrategyInterface::SELL_GAIN_PERCENT
                )
            ;
//             $table = $conn
//                 ->newTable($setup->getTable(\Kobens\Gemini\Model\ResourceModel\Position\BTC\USD::MAIN_TABLE))
//                 ->addColumn(
//                     'order_id',
//                     Table::TYPE_INTEGER,
//                     null,
//                     ['identity' => true, 'unsigned' => true, 'nullable' => false],
//                     'Order Id'
//                 )
//                 ->addColumn(
//                     'exchange',
//                     Table::TYPE_TEXT,
//                     255,
//                     ['default' => null, 'nullable' => false],
//                     'Exchange the order was placed on'
//                 )
//                 ->addColumn(
//                     'side'
//                 )
//             ;
        }
    }
    
}
