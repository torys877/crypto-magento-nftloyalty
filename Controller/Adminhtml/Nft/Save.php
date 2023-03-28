<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Crypto\NftLoyalty\Controller\Adminhtml\Nft;

use Crypto\NftLoyalty\Api\Data\NftInterface;
use Crypto\NftLoyalty\Controller\Adminhtml\Entity;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Entity implements HttpPostActionInterface
{
    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create($this->resultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*');

        /** @var array $requestData */
        $requestData = $this->getRequest()->getParam('general') ?? [];
        if ($requestData) {
            try {
                $nftId = isset($requestData[NftInterface::ENTITY_ID])
                    ? (int) $requestData[NftInterface::ENTITY_ID]
                    : null;

                if ($nftId) {
                    $nft = $this->nftRepository->getById((int) $nftId);
                } else {
                    $nft = $this->nftFactory->create();
                }

                $nft->setNftName((string) $requestData[NftInterface::NFT_NAME] ?? $nft->getNftName());
                $nft->setNftAddress((string) $requestData[NftInterface::NFT_ADDRESS] ?? $nft->getNftAddress());
                $nft->setNftSymbol((string) $requestData[NftInterface::NFT_SYMBOL] ?? $nft->getNftSymbol());
                $nft->setNftContractAbi((string) $requestData[NftInterface::NFT_CONTRACT_ABI] ?? $nft->getNftContractAbi());

                $nft = $this->nftRepository->save($nft);

                $this->messageManager->addSuccessMessage((string) __(
                    'NFT has been successfully saved.'
                ));

                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath($this->_redirect->getRefererUrl());
                } elseif ($this->getRequest()->getParam('redirect_to_new')) {
                    $resultRedirect->setPath('*/*/edit', [NftInterface::ENTITY_ID => $nft->getId()]);
                }
            } catch (LocalizedException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
                $resultRedirect->setPath($this->_redirect->getRefererUrl());
            }
        }

        return $resultRedirect;
    }
}
