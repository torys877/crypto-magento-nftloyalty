<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */

namespace Crypto\NftLoyalty\Model;

use Magento\Quote\Model\Quote;
use Crypto\NftLoyalty\Api\Data\NftInterface;
use Crypto\NftLoyalty\Api\Data\NftQuoteInterface;
use Crypto\NftLoyalty\Api\Data\NftQuoteInterfaceFactory;
use Crypto\NftLoyalty\Model\Data\NftQuoteFactory;
use Crypto\NftLoyalty\Helper\Config;
use Psr\Log\LoggerInterface;

class NftHandler
{
    private NftQuoteRepository $nftQuoteRepository;
    private NftQuoteFactory $nftQuoteFactory;
    private NftRepository $nftRepository;
    private Config $config;
    private LoggerInterface $logger;

    public function __construct(
        NftQuoteRepository $nftQuoteRepository,
        NftQuoteFactory $nftQuoteFactory,
        NftRepository $nftRepository,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->nftQuoteRepository = $nftQuoteRepository;
        $this->nftQuoteFactory = $nftQuoteFactory;
        $this->nftRepository = $nftRepository;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @param Quote $quote
     * @param string $customerAddress
     * @return bool
     */
    public function process(Quote $quote): bool
    {
        $customerAddress = $quote->getData('customer_evm_address');

        if (!$customerAddress) {
            return false;
        }

        $nfts = $this->findNotEmptyNftByCustomerAddress($customerAddress);

        $result = false;

        if (!empty($nfts)) {
            $result = true;

            foreach ($nfts as $nftId => $nftData) {
                $nftQuote = $this->nftQuoteRepository->getByQuoteAndNftId($quote->getId(), $nftId);

                if ($nftQuote->getId()) {
                    continue;
                }

                /** @var NftQuoteInterface $nftQuote */
                $nftQuote = $this->nftQuoteFactory->create();
                $nftQuote->setNftId($nftId)
                    ->setQuoteId($quote->getId())
                    ->setBalanceOf($nftData['balance_of']);

                $this->nftQuoteRepository->save($nftQuote);
            }
        }

        return $result;
    }

    /**
     * find nft with not zero balance for customer address, using RPC endpoint (for example Infura)
     *
     * @param $customerAddress
     * @return NftInterface[]
     */
    public function findNotEmptyNftByCustomerAddress($customerAddress): array
    {
        $resultNft = [];
        $allNftCollection = $this->nftRepository->getAll();
        if ($allNftCollection->count()) {
            foreach ($allNftCollection->getItems() as $nftItem) {
                /** @var NftInterface $nftItem */

                try {
                    if (strpos($nftItem->getNftAddress(), '0x') === false) { //check only for ethereum, todo - need change
                        continue;
                    }

                    //request balance of NFT
                    $resultData = $this->config->getNftBalance($customerAddress, $nftItem->getNftAddress());
                    $err = $resultData['error'];
                    $result = $resultData['result'];

                    if ($err) {
                        $this->logger->error(json_encode($err));
                    }

                    if ($result > 0) {
                        $resultNft[$nftItem->getId()]['item'] = $nftItem;
                        $resultNft[$nftItem->getId()]['balance_of'] = $result;
                    }

                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                    $this->logger->error($e->getTraceAsString());
                }
            }
        }

        return $resultNft;
    }
}
