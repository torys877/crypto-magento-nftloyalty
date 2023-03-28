<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Crypto\NftLoyalty\Model;

use Crypto\NftLoyalty\Api\Data\NftInterface;
use Crypto\NftLoyalty\Api\NftQuoteRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Crypto\NftLoyalty\Model\ResourceModel\NftQuote as NftQuoteResource;
use Crypto\NftLoyalty\Model\Data\NftQuoteFactory;
use Crypto\NftLoyalty\Api\Data\NftQuoteInterface;
use Crypto\NftLoyalty\Model\Data\NftQuote as NftQuoteEntity;
use Crypto\NftLoyalty\Model\ResourceModel\NftQuote\CollectionFactory as CollectionFactory;
use Crypto\NftLoyalty\Model\ResourceModel\NftQuote\Collection;

class NftQuoteRepository implements NftQuoteRepositoryInterface
{

    private NftQuoteResource $nftResource;
    private NftQuoteFactory $nftFactory;
    private CollectionFactory $collectionFactory;

    public function __construct(
        NftQuoteResource $nftResource,
        NftQuoteFactory $nftFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->nftResource = $nftResource;
        $this->nftFactory = $nftFactory;
        $this->collectionFactory = $collectionFactory;
    }

    public function getById(int $entityId): NftQuoteInterface
    {
        /** @var NftQuoteEntity $nft */
        $nft = $this->nftFactory->create();
        $this->nftResource->load($nft, $entityId);

        if (!$nft->getEntityId()) {
            throw new NoSuchEntityException(__('The NFT Quote with the "%1" ID doesn\'t exist.', $entityId));
        }

        return $nft;
    }

    public function getByNftAddress(string $nftAddress): NftInterface
    {
        /** @var NftQuoteEntity $nft */
        $nft = $this->nftFactory->create();
        $this->nftResource->load($nft, $nftAddress, NftInterface::NFT_ADDRESS);

        if (!$nft->getEntityId()) {
            throw new NoSuchEntityException(__('The NFT with address "%1" doesn\'t exist in table.', $nftAddress));
        }

        return $nft;
    }

    public function save(NftQuoteInterface $nft): NftQuoteInterface
    {
        try {
            $this->nftResource->save($nft); // @phpstan-ignore-line
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $nft;
    }

    public function getListByQuoteId(int $quoteId): Collection
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(NftQuoteInterface::QUOTE_ID, ['eq' => $quoteId]);

        return $collection;
    }

    public function getByQuoteAndNftId(int $quoteId, int $nftId): NftQuoteInterface
    {

        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(NftQuoteInterface::QUOTE_ID, ['eq' => $quoteId]);
        $collection->addFieldToFilter(NftQuoteInterface::NFT_ID, ['eq' => $nftId]);

        return $collection->getFirstItem();
    }
}
