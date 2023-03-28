<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Crypto\NftLoyalty\Model;

use Crypto\NftLoyalty\Model\NftQuoteRepository;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\UrlInterface;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\StoreManagerInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;


    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var Quote
     */
    protected $quote;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    private NftQuoteRepository $nftQuoteRepository;

    public function __construct(
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        CheckoutSession $checkoutSession,
        NftQuoteRepository $nftQuoteRepository
    ) {
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->nftQuoteRepository = $nftQuoteRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $nftsQuoteCollection = $this->nftQuoteRepository->getListByQuoteId((int) $this->getQuote()->getId());

        $config = [];
        if ($nftsQuoteCollection->count()) {
            $config = [
                'totalsData' => [
                    'nft' => [
                        'label' => __('NFT\'s are applied') // @phpstan-ignore-line
                    ]
                ]
            ];
        }

        return $config;
    }

    /**
     * Retrieve Quote object
     *
     * @return Quote
     */
    protected function getQuote()
    {
        if (!$this->quote) {
            $this->quote = $this->checkoutSession->getQuote();
        }

        return $this->quote;
    }
}
