<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */

namespace Crypto\NftLoyalty\Model\Nft;

use Crypto\NftLoyalty\Api\NftManagementInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\QuoteRepository;
use Crypto\NftLoyalty\Model\NftHandler;
use Crypto\NftLoyalty\Model\NftQuoteRepository;

class NftManagement implements NftManagementInterface
{
    private QuoteIdMaskFactory $quoteIdMaskFactory;
    private QuoteRepository $quoteRepository;
    private NftHandler $nftHandler;
    private NftQuoteRepository $nftQuoteRepository;

    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        QuoteRepository $quoteRepository,
        NftHandler $nftHandler,
        NftQuoteRepository $nftQuoteRepository
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->quoteRepository = $quoteRepository;
        $this->nftHandler = $nftHandler;
        $this->nftQuoteRepository = $nftQuoteRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function get($cartId)
    {
        return '';
    }

    /**
     * Add a nft by code to a specified cart.
     *
     * @param string $cartId The cart ID.
     * @param string $customerAddress The customer address for applying NFT.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified could not be added.
     */
    public function apply($cartId, $customerAddress)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        $cartId = $quoteIdMask->getQuoteId();
        $quote = $this->quoteRepository->getActive($cartId);

        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('The "%1" Cart doesn\'t contain products.', $cartId));
        }

        if (!$quote->getStoreId()) {
            throw new NoSuchEntityException(__('Cart isn\'t assigned to correct store'));
        }

        try {
            $quote->setData('customer_evm_address', $customerAddress);
            $this->nftHandler->process($quote);

            $quote->getShippingAddress()->setCollectShippingRates(true);
            $this->quoteRepository->save($quote->collectTotals());

            $nftQuoteCollection = $this->nftQuoteRepository->getListByQuoteId($quote->getId());

            if ($nftQuoteCollection->count()) {
                $result = true;
            } else {
                $result = false;
            }
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __("The NFT code couldn't be applied. Something went wrong"),
                $e
            );
        }

        return $result;
    }
}
