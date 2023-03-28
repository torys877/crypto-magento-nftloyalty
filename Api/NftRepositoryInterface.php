<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Crypto\NftLoyalty\Api;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Crypto\NftLoyalty\Api\Data\NftInterface;
use Crypto\NftLoyalty\Model\ResourceModel\Nft\Collection;

interface NftRepositoryInterface
{
    /**
     * @param int $nftId
     * @return NftInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $nftId): NftInterface;

    /**
     * @param string $nftAddress
     * @return NftInterface
     * @throws NoSuchEntityException
     */
    public function getByNftAddress(string $nftAddress): NftInterface;

    /**
     *
     * @param NftInterface $nft
     * @return NftInterface
     * @throws CouldNotSaveException
     */
    public function save(NftInterface $nft): NftInterface;


    /**
     * @return Collection
     */
    public function getAll(): Collection;
}
