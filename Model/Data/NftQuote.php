<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Crypto\NftLoyalty\Model\Data;

use Crypto\NftLoyalty\Api\Data\NftQuoteInterface;
use Crypto\NftLoyalty\Model\ResourceModel\Nft as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class NftQuote extends AbstractModel implements NftQuoteInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'cryptom2_nft_quote_model';

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
     * Getter for NftId.
     *
     * @return int|null
     */
    public function getNftId(): ?int
    {
        return $this->getData(self::NFT_ID) === null ? null
            : (int)$this->getData(self::NFT_ID);
    }

    /**
     * Setter for NftId.
     *
     * @param int|null $nftId
     *
     * @return self
     */
    public function setNftId(?int $nftId): self
    {
        $this->setData(self::NFT_ID, $nftId);

        return $this;
    }

    public function getQuoteId(): ?int
    {
        return $this->getData(self::QUOTE_ID) === null ? null
            : (int)$this->getData(self::QUOTE_ID);
    }

    public function setQuoteId(int $quoteId): NftQuoteInterface
    {
        $this->setData(self::QUOTE_ID, $quoteId);

        return $this;
    }

    public function getBalanceOf(): ?int
    {
        return $this->getData(self::BALANCE_OF) === null ? null
            : (int)$this->getData(self::BALANCE_OF);
    }

    public function setBalanceOf(int $balanceOf): NftQuoteInterface
    {
        $this->setData(self::BALANCE_OF, $balanceOf);

        return $this;
    }
}
