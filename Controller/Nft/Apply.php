<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Crypto\NftLoyalty\Controller\Nft;

use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Store\Model\StoreManagerInterface;
use Crypto\NftLoyalty\Model\NftHandler;
use Psr\Log\LoggerInterface;
use Magento\Quote\Model\QuoteRepository;
use Magento\Checkout\Controller\Cart;

class Apply extends Cart implements HttpPostActionInterface
{
    private JsonFactory $jsonFactory;
    private NftHandler $nftHandler;
    private LoggerInterface $logger;
    private QuoteRepository $quoteRepository;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        StoreManagerInterface $storeManager,
        Validator $formKeyValidator,
        JsonFactory $jsonFactory,
        CustomerCart $cart,
        NftHandler $nftHandler,
        LoggerInterface $logger,
        QuoteRepository $quoteRepository
    ) {
        parent::__construct($context, $scopeConfig, $checkoutSession, $storeManager, $formKeyValidator, $cart);

        $this->jsonFactory = $jsonFactory;
        $this->nftHandler = $nftHandler;
        $this->logger = $logger;
        $this->quoteRepository = $quoteRepository;
    }

    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $requestQuoteId = $this->_request->getParam('quote_id');
        $requestCustomerAddress = $this->_request->getParam('customer_address');

        if ($requestQuoteId != $this->cart->getQuote()->getId()) {
            return $resultJson->setData(
                [
                    'success' => false,
                    'message' => __('Quote Id Is Incorrect. Please, update page.')
                ]
            );
        }

        $message = '';
        $result = false;

        try {
            $quote = $this->cart->getQuote();
            $quote->setData('customer_evm_address', $requestCustomerAddress);
            $this->quoteRepository->save($quote);

            $result = $this->nftHandler->process($this->cart->getQuote());
            if ($result) {
                $message = __('NFT Successfully Applied');
                $this->messageManager->addSuccessMessage((string) $message);
            } else {
                $message = __('You don\'t have NFT for discount');
                $this->messageManager->addWarningMessage((string) $message);
            }
        } catch (\Exception $e) {
            $message = __('Something went wrong.');
            $this->messageManager->addErrorMessage((string) $message);
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
        }

        return $resultJson->setData(
            [
                'result' => true,
                'message' => $message
            ]
        );
    }
}
