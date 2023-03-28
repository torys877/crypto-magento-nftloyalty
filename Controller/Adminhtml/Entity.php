<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Crypto\NftLoyalty\Controller\Adminhtml;

use Crypto\NftLoyalty\Model\NftRepository;
use Crypto\NftLoyalty\Model\Data\NftFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

abstract class Entity extends Action
{
    protected NftRepository $nftRepository;
    protected NftFactory $nftFactory;
    protected JsonFactory $jsonFactory;

    public function __construct(
        Context $context,
        NftRepository $nftRepository,
        NftFactory $nftFactory,
        JsonFactory $jsonFactory
    ) {
        $this->nftRepository = $nftRepository;
        $this->nftFactory = $nftFactory;
        $this->jsonFactory = $jsonFactory;

        parent::__construct($context);
    }
}
