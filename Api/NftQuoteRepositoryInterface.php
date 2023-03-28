<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Crypto\NftLoyalty\Api;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Crypto\NftLoyalty\Api\Data\NftQuoteInterface;
use Crypto\NftLoyalty\Model\ResourceModel\NftQuote\Collection;

interface NftQuoteRepositoryInterface
{
    /**
     * @param int $entityId
     * @return NftQuoteInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId): NftQuoteInterface;

    /**
     * @param int $quoteId
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function getListByQuoteId(int $quoteId): Collection;

    /**
     * @param string $quoteId
     * @param int $nftId
     * @return NftQuoteInterface
     * @throws NoSuchEntityException
     */
    public function getByQuoteAndNftId(int $quoteId, int $nftId): NftQuoteInterface;

    /**
     *
     * @param NftQuoteInterface $nft
     * @return NftQuoteInterface
     * @throws CouldNotSaveException
     */
    public function save(NftQuoteInterface $nft): NftQuoteInterface;
}
