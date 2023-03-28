<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Crypto\NftLoyalty\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use phpseclib3\Math\BigInteger;
use Web3\Contract;

class Config
{
    public const CONFIG_NFT_IS_ENABLED = 'cryptonftloyalty/general/active';
    public const CONFIG_NFT_CONTRACT_ABI = 'cryptonftloyalty/general/nft_smart_contract_abi';
    public const CONFIG_RPC_ENDPOINT = 'cryptonftloyalty/general/rpc_endpoint';

    private ScopeConfigInterface $scopeConfig;
    private StoreManagerInterface $storeManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled(): ?bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_NFT_IS_ENABLED);
    }

    public function getNftContractAbi(): ?string
    {
        return (string) $this->scopeConfig->getValue(self::CONFIG_NFT_CONTRACT_ABI, ScopeInterface::SCOPE_STORES, $this->storeManager->getStore()->getId());
    }

    public function getRpcEndpoint(): ?string
    {
        return (string) $this->scopeConfig->getValue(self::CONFIG_RPC_ENDPOINT, ScopeInterface::SCOPE_STORES, $this->storeManager->getStore()->getId());
    }

    /**
     * @param $customerAddress
     * @param $nftAddress
     * @return array
     */
    public function getNftBalance($customerAddress, $nftAddress)
    {
        $contract = new Contract($this->getRpcEndpoint(), $this->getNftContractAbi());

        $result = '';
        $err = null;
        $contract->at($nftAddress)->call('balanceOf', $customerAddress, function($errInternal, $data) use (&$err, &$result) {
            $err = $errInternal;

            if (isset($data[0]) && $data[0] instanceof BigInteger) {
                $result = $data[0]->toString();
            } else {
                $result = 0;
            }
        });

        return [
            'error' => $err,
            'result' => $result
        ];
    }
}
