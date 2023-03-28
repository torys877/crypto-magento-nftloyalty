<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Crypto\NftLoyalty\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Crypto\NftLoyalty\Api\Data\NftInterface;

class Nft extends AbstractDb
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'cryptom2_nft_resource_model';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('cryptom2_nft_loyalty', NftInterface::ENTITY_ID);
        $this->_useIsObjectNew = true;
    }
}
