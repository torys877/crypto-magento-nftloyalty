<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Crypto\NftLoyalty\Api\Data;

interface NftInterface
{
    /**
     * String constants for property names
     */
    const ENTITY_ID = "entity_id";
    const NFT_ADDRESS = "nft_address";
    const NFT_NAME = "nft_name";
    const NFT_SYMBOL = "nft_symbol";
    const NFT_CONTRACT_ABI = "nft_contract_abi";

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
     * Getter for NftAddress.
     * @return string|null
     */
    public function getNftAddress(): ?string;

    /**
     * Setter for NftAddress.
     *
     * @param string $nftAddress
     * @return self
     */
    public function setNftAddress(string $nftAddress): self;

    /**
     * Getter for NftName.
     * @return string|null
     */
    public function getNftName(): ?string;

    /**
     * Setter for NftName.
     *
     * @param string $nftName
     * @return self
     */
    public function setNftName(string $nftName): self;

    /**
     * Getter for NftName.
     * @return string|null
     */
    public function getNftSymbol(): ?string;

    /**
     * Setter for NftName.
     *
     * @param string $nftSymbol
     * @return self
     */
    public function setNftSymbol(string $nftSymbol): self;

    /**
     * Getter for NftContractAbi.
     * @return string|null
     */
    public function getNftContractAbi(): ?string;

    /**
     * Setter for NftContractAbi.
     *
     * @param string $nftAbi
     * @return self
     */
    public function setNftContractAbi(string $nftAbi): self;
}
