<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Crypto\NftLoyalty\Model\ResourceModel\Nft;

use Crypto\NftLoyalty\Model\Data\Nft as Model;
use Crypto\NftLoyalty\Model\ResourceModel\Nft as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'cryptom2_nft_collection';

    /**
     * Initialize collection model.
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }

    protected function _toOptionArray($valueField = Model::ENTITY_ID, $labelField = Model::NFT_NAME, $additional = [])
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}
