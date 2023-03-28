<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Crypto\NftLoyalty\Model;

use Crypto\NftLoyalty\Api\Data\NftInterface;
use Crypto\NftLoyalty\Api\NftRepositoryInterface;
use Crypto\NftLoyalty\Model\ResourceModel\Nft\Collection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Crypto\NftLoyalty\Model\ResourceModel\Nft as NftResource;
use Crypto\NftLoyalty\Model\Data\NftFactory;
use Crypto\NftLoyalty\Model\Data\Nft as NftEntity;
use Crypto\NftLoyalty\Model\ResourceModel\Nft\CollectionFactory as CollectionFactory;

class NftRepository implements NftRepositoryInterface
{

    private NftResource $nftResource;
    private NftFactory $nftFactory;
    private CollectionFactory $collectionFactory;

    public function __construct(
        NftResource $nftResource,
        NftFactory $nftFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->nftResource = $nftResource;
        $this->nftFactory = $nftFactory;
        $this->collectionFactory = $collectionFactory;
    }

    public function getById(int $nftId): NftInterface
    {
        /** @var NftEntity $nft */
        $nft = $this->nftFactory->create();
        $this->nftResource->load($nft, $nftId);

        if (!$nft->getEntityId()) {
            throw new NoSuchEntityException(__('The NFT with the "%1" ID doesn\'t exist.', $nftId));
        }

        return $nft;
    }

    public function getByNftAddress(string $nftAddress): NftInterface
    {
        if (empty($nftAddress)) {
            return $this->nftFactory->create();
        }

        /** @var NftEntity $nft */
        $nft = $this->nftFactory->create();
        $this->nftResource->load($nft, $nftAddress, NftInterface::NFT_ADDRESS);

        if (!$nft->getEntityId()) {
//            throw new NoSuchEntityException(__('The NFT with address "%1" doesn\'t exist in table.', $nftAddress));
            return $this->nftFactory->create();
        }

        return $nft;
    }

    public function save(NftInterface $nft): NftInterface
    {
        try {
            $this->nftResource->save($nft); // @phpstan-ignore-line
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $nft;
    }

    public function getAll(): Collection
    {
        return $this->collectionFactory->create();
    }
}
