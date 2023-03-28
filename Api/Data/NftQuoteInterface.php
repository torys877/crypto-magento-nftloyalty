<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Crypto\NftLoyalty\Api\Data;

interface NftQuoteInterface
{
    /**
     * String constants for property names
     */
    const ENTITY_ID = "entity_id";
    const NFT_ID = "nft_id";
    const QUOTE_ID = "quote_id";
    const BALANCE_OF = "balance_of";

    /**
     * Identifier getter
     *
     * @return mixed
     */
    public function getId();

    /**
     * Identifier setter
     *
     * @param mixed $value
     * @return $this
     */
    public function setId($value);

    /**
     * Getter for NftId.
     *
     * @return int|null
     */
    public function getNftId(): ?int;

    /**
     * Setter for NftId.
     *
     * @param int $nftId
     *
     * @return self
     */
    public function setNftId(int $nftId): self;

    /**
     * Getter for QuoteId.
     *
     * @return int|null
     */
    public function getQuoteId(): ?int;

    /**
     * Setter for QuoteId.
     *
     * @param int $quoteId
     *
     * @return self
     */
    public function setQuoteId(int $quoteId): self;

    /**
     * Getter for Balance Of Nft.
     *
     * @return int|null
     */
    public function getBalanceOf(): ?int;

    /**
     * Setter for Balance Of Nft.
     *
     * @param int $balanceOf
     *
     * @return self
     */
    public function setBalanceOf(int $balanceOf): self;
}
