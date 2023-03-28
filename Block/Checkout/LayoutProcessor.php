<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace Crypto\NftLoyalty\Block\Checkout;

use Crypto\NftLoyalty\Helper\Config;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

/**
 * Checkout Layout Processor
 */
class LayoutProcessor implements LayoutProcessorInterface
{
    private Config $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        $isEnabled = $this->config->isEnabled();

        if (isset(
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']
            ['children']['afterMethods']['children']['nft']
        ) && !$isEnabled) {
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']
                ['children']['afterMethods']['children']['nft']['config']['componentDisabled'] = true;
        }

        if (isset(
            $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']
            ['nft']
        ) && !$isEnabled) {
            $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']
                ['nft']['config']['componentDisabled'] = true;
        }

        return $jsLayout;
    }
}
