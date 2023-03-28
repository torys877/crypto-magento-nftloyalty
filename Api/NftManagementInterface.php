<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */

namespace Crypto\NftLoyalty\Api;

/**
 * Nft Loyalty management interface.
 * @api
 * @since 100.0.2
 */
interface NftManagementInterface
{
    /**
     * Return information for a nft in a specified cart.
     *
     * @param string $cartId The cart ID.
     * @return string The nft data.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function get($cartId);

    /**
     * Add a nft by code to a specified cart.
     *
     * @param string $cartId The cart ID.
     * @param string $customerAddress The customer address for applying NFT.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified could not be added.
     */
    public function apply($cartId, $customerAddress);
}
