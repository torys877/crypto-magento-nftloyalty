<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Crypto\NftLoyalty\Ui\Component\Form;

use Crypto\NftLoyalty\Model\ResourceModel\Nft\CollectionFactory as NftCollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    public function __construct(
        NftCollectionFactory $nftCollectionFactory, // @phpstan-ignore-line
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $nftCollectionFactory->create(); // @phpstan-ignore-line
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
}
