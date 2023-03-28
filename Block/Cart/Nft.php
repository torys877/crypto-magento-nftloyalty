<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */

namespace Crypto\NftLoyalty\Block\Cart;

use Magento\Captcha\Block\Captcha;
use Crypto\NftLoyalty\Helper\Config;

/**
 * Block with apply-coupon form.
 *
 * @api
 * @since 100.0.2
 */
class Nft extends \Magento\Checkout\Block\Cart\AbstractCart
{
    private Config $config;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $checkoutSession, $data);
        $this->_isScopePrivate = true;
        $this->config = $config;
    }

    /**
     * Applied code.
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function isNftApplied()
    {
        return (bool) $this->getQuote()->getData('customer_evm_address');
    }

    public function getNftContractAbi()
    {
        return $this->config->getNftContractAbi();
    }

    public function getApplyNftUrl()
    {
        return $this->_urlBuilder->getUrl('cryptonftloyalty/nft/apply');
    }

    public function isEnabled()
    {
        return $this->config->isEnabled();
    }
}
