<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */

namespace Crypto\NftLoyalty\Model\Rule\Condition;

use Magento\Backend\Model\UrlInterface;
use Crypto\NftLoyalty\Api\Data\NftQuoteInterface;
use Crypto\NftLoyalty\Model\NftRepository;
use Crypto\NftLoyalty\Model\NftQuoteRepository;
use Psr\Log\LoggerInterface;
use Crypto\NftLoyalty\Helper\Config;

/**
 * Cart NFT Rule Validator
 */
class NftAddress extends \Magento\Rule\Model\Condition\AbstractCondition
{
    const ATTRIBUTE_VALUE = 'nft_address';
    const ATTRIBUTE_LABEL = 'NFT Address';

    private UrlInterface $url;
    private NftRepository $nftRepository;
    private NftQuoteRepository $nftQuoteRepository;
    private LoggerInterface $logger;
    private Config $config;

    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        UrlInterface $url,
        NftRepository $nftRepository,
        NftQuoteRepository $nftQuoteRepository,
        LoggerInterface $logger,
        Config $config,
        array $data = []
    ) {
        $this->url = $url;
        $this->nftRepository = $nftRepository;
        $this->nftQuoteRepository = $nftQuoteRepository;
        $this->logger = $logger;
        $this->config = $config;

        parent::__construct($context, $data);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            self::ATTRIBUTE_VALUE    => __(self::ATTRIBUTE_LABEL)
        ];
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        return 'multiselect';
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        return 'multiselect';
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttributeElement()
    {
        //@todo remove
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    /**
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        if (!$this->config->isEnabled()) {
            return false;
        }

        $quote = $model;

        if ($quote instanceof \Magento\Quote\Model\Quote\Address) {
            $quote = $model->getQuote();
        }

        //nft address, need load nft ID
        $attributeValue = $quote->getData('customer_evm_address');

        if (!$attributeValue) {
            return false;
        }

        $nftQuoteCollection = $this->nftQuoteRepository->getListByQuoteId((int) $quote->getId());

        if (!$nftQuoteCollection->count()) {
            return false;
        }

        try {
            $nftsArray = [];
            foreach ($nftQuoteCollection->getItems() as $nftQuoteItem) {
                /** @var NftQuoteInterface $nftQuoteItem */
                $nftsArray[] = $nftQuoteItem->getNftId();
            }

            if (empty($nftsArray)) {
                return false;
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
            return false;
        }

        return $this->validateAttribute($nftsArray);
    }

    /**
     * Specify allowed comparison operators
     *
     * @return $this
     */
    public function loadOperatorOptions()
    {
        parent::loadOperatorOptions();

        $this->setOperatorOption(
            [
                '()' => __('is one of'),
                '!()' => __('is not one of'),
            ]
        );

        return $this;
    }

    /**
     * Get value select options
     *
     * @return array|mixed
     */
    public function getValueSelectOptions()
    {
        $nftCollection = $this->nftRepository->getAll();

        return $nftCollection->toOptionArray();
    }
}
