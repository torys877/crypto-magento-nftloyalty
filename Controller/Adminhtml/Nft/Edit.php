<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Crypto\NftLoyalty\Controller\Adminhtml\Nft;

use Crypto\NftLoyalty\Controller\Adminhtml\Entity;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class Edit extends Entity implements HttpGetActionInterface
{
    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var int $nftId */
        $nftId = (int) $this->getRequest()->getParam('entity_id', 0);

        if ($nftId) {
            try {
                $this->nftRepository->getById($nftId);
            } catch (LocalizedException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());

                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultFactory->create($this->resultFactory::TYPE_REDIRECT);
                return $resultRedirect->setPath('*/*');
            }
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create($this->resultFactory::TYPE_PAGE);

        $resultPage->setActiveMenu('Crypto_NftLoyalty::nft_items');
        $resultPage->getConfig()->getTitle()->prepend(
            (string) __($nftId ? 'Edit NFT' : 'New NFT')
        );

        return $resultPage;
    }
}
