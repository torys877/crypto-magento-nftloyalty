<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Crypto\NftLoyalty\Model\Data;

use Crypto\NftLoyalty\Api\Data\NftInterface;
use Crypto\NftLoyalty\Model\ResourceModel\Nft as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class Nft extends AbstractModel implements NftInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'cryptom2_nft_model';

    /**
     * Initialize magento model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * Getter for NftAddress.
     *
     * @return string|null
     */
    public function getNftAddress(): ?string
    {
        return $this->getData(self::NFT_ADDRESS) === null ? null
            : (string)$this->getData(self::NFT_ADDRESS);
    }

    /**
     * Setter for NftAddress.
     *
     * @param int|string $nftAddress
     *
     * @return self
     */
    public function setNftAddress(?string $nftAddress): self
    {
        $this->setData(self::NFT_ADDRESS, $nftAddress);

        return $this;
    }

    public function getNftName(): ?string
    {
        return $this->getData(self::NFT_NAME) === null ? null
            : (string)$this->getData(self::NFT_NAME);
    }

    public function setNftName(string $nftName): NftInterface
    {
        $this->setData(self::NFT_NAME, $nftName);

        return $this;
    }

    public function getNftSymbol(): ?string
    {
        return $this->getData(self::NFT_SYMBOL) === null ? null
            : (string)$this->getData(self::NFT_SYMBOL);
    }

    public function setNftSymbol(string $nftSymbol): NftInterface
    {
        $this->setData(self::NFT_SYMBOL, $nftSymbol);

        return $this;
    }

    public function getNftContractAbi(): ?string
    {
        return $this->getData(self::NFT_CONTRACT_ABI) === null ? null
            : (string)$this->getData(self::NFT_CONTRACT_ABI);
    }

    public function setNftContractAbi(string $nftAbi): NftInterface
    {
        $this->setData(self::NFT_CONTRACT_ABI, $nftAbi);

        return $this;
    }
}
