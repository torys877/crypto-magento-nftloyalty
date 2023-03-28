<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */

namespace Crypto\NftLoyalty\Observer\Admin;

use Crypto\NftLoyalty\Model\Rule\Condition\NftAddress;

class AddNewConditionHandle implements \Magento\Framework\Event\ObserverInterface
{
    const GROUP_NAME = 'NFT Conditions';

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return AddNewConditionHandle
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $additional = $observer->getAdditional();
        $conditions = $additional->getConditions();

        if (!is_array($conditions)) {
            $conditions = [];
        }

        $conditions[] = [
            'value' => [
                NftAddress::ATTRIBUTE_VALUE => [
                    'value' => NftAddress::class . '|' . NftAddress::ATTRIBUTE_VALUE,
                    'label' => __(NftAddress::ATTRIBUTE_LABEL)
                ]
            ],
            'label' => __(self::GROUP_NAME),
        ];

        $additional->setConditions($conditions);

        return $this;
    }
}
