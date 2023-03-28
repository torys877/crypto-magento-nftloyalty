<?php
/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */

namespace Amasty\Conditions\Block\Adminhtml\Customoptions;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter as OptionsConverter;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Model\Config\Source\Product\Options\Type as OptionsType;
use Magento\Catalog\Model\ResourceModel\Product\Option\Collection as OptionsCollection;

/**
 * Product Custom Options Grid
 * @since 1.4.0
 */
class Chooser extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Option\CollectionFactory
     */
    private $optionsFactory;

    /**
     * @var OptionsType
     */
    private $optionsType;

    /**
     * @var OptionsConverter
     */
    private $optionsConverter;

    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $attributeRepository;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ResourceModel\Product\Option\CollectionFactory $optionsFactory,
        OptionsType $optionsType,
        OptionsConverter $optionsConverter,
        ProductAttributeRepositoryInterface $attributeRepository,
        array $data = []
    ) {
        $this->optionsFactory = $optionsFactory;
        $this->optionsType = $optionsType;
        $this->optionsConverter = $optionsConverter;
        $this->attributeRepository = $attributeRepository;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize grid
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        if ($this->getRequest()->getParam('current_grid_id')) {
            $this->setId($this->getRequest()->getParam('current_grid_id'));
        } else {
            $this->setId('customoptions_grid_chooser_' . $this->getId());
        }

        $this->setDefaultSort('option_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);

        $form = $this->getRequest()->getParam('form');
        if ($form) {
            $this->setRowClickCallback("{$form}.chooserGridRowClick.bind({$form})");
            $this->setCheckboxCheckCallback("{$form}.chooserGridCheckboxCheck.bind({$form})");
            $this->setRowInitCallback("{$form}.chooserGridRowInit.bind({$form})");
        }
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }

    /**
     * Add titles and product sku to custom options collection
     * Set collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        /** @var $collection OptionsCollection */
        $collection = $this->optionsFactory->create();
        $collection->getSelect()->columns(['sku' => 'cpe.sku'])
            ->join(
                ['default_option_title' => $collection->getTable('catalog_product_option_title')],
                'default_option_title.option_id = main_table.option_id',
                ['title']
            )->where(
                'default_option_title.store_id = ?',
                \Magento\Store\Model\Store::DEFAULT_STORE_ID
            );

        $this->joinProductName($collection);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Join default product name to collection
     *
     * @param OptionsCollection $collection
     */
    private function joinProductName($collection)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $nameAttribute */
        $nameAttribute = $this->attributeRepository->get(ProductAttributeInterface::CODE_NAME);
        $collection->getSelect()->join(
            ['name_attr' => $nameAttribute->getBackend()->getTable()],
            sprintf(
                'name_attr.%s = main_table.product_id AND name_attr.attribute_id = %s AND name_attr.store_id = %d',
                $nameAttribute->getEntityIdField(),
                $nameAttribute->getId(),
                \Magento\Store\Model\Store::DEFAULT_STORE_ID
            ),
            ['product_name' => 'name_attr.value']
        );
    }

    /**
     * Row click javascript callback getter
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        return $this->_getData('row_click_callback');
    }

    /**
     * Prepare columns for grid
     *
     * @return $this
     */
    public function _prepareColumns()
    {
        $this->addColumn(
            'selected_options',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'selected_options',
                'values' => $this->getRequest()->getPost('selected', []),
                'align' => 'center',
                'index' => 'option_id',
                'filter_index' => 'main_table.option_id',
                'filter_condition_callback' => [$this, 'filterSelectedOptions']
            ]
        );
        $this->addColumn(
            'option_id',
            [
                'type' => 'number',
                'header' => __('Option ID'),
                'name' => 'option_id',
                'index' => 'option_id',
                'filter_index' => 'main_table.option_id'
            ]
        );
        $this->addColumn(
            'title',
            [
                'header' => __('Option Title'),
                'name' => 'title',
                'index' => 'title',
                'filter_index' => 'default_option_title.title'
            ]
        );

        $this->addOptionsTypeColumn();

        $this->addColumn(
            'product_sku',
            [
                'type' => 'text',
                'header' => __('Product SKU'),
                'name' => 'sku',
                'index' => 'sku',
                'filter_index' => 'cpe.sku'
            ]
        );
        $this->addColumn(
            'product_name',
            [
                'type' => 'text',
                'header' => __('Product Name'),
                'name' => 'product_name',
                'index' => 'product_name',
                'filter_index' => 'name_attr.value'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Add column with custom option types
     *
     * @return $this
     */
    private function addOptionsTypeColumn()
    {
        $optionTypeGroups = $this->optionsType->toOptionArray();

        $options = [];

        foreach ($optionTypeGroups as $index => $item) {
            if (is_array($item['value'])) {
                //for properly convert row value to option label, options should be as flat array without groups
                $options += $this->optionsConverter->toFlatArray($item['value']);
            } elseif ($item['value'] == '') {
                //delete empty option "Please select"
                unset($optionTypeGroups[$index]);
            }
        }

        $this->addColumn(
            'type',
            [
                'header' => __('Option Type'),
                'name' => 'type',
                'index' => 'type',
                'type' => 'options',
                'filter_index' => 'main_table.type',
                'option_groups' => $optionTypeGroups,
                'options' => $options
            ]
        );

        return $this;
    }

    /**
     * Filter for checkbox column (show only selected/unselected rows)
     *
     * @param OptionsCollection $collection
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     */
    public function filterSelectedOptions($collection, $column)
    {
        /** @var \Magento\Backend\Block\Widget\Grid\Column\Filter\Checkbox $filter */
        $filter = $column->getFilter();
        $filterValue = $filter->getValue();
        if (!in_array($filterValue, ['0', '1'], true)) {
            return;
        }
        $field = $column->getFilterIndex() ?: $column->getIndex();
        $condition = 'in';
        if ($filterValue === '0') {
            $condition = 'nin';
        }

        $collection->addFieldToFilter($field, [$condition => $column->getValues()]);
    }

    /**
     * Grid URL getter for ajax mode
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            \Amasty\Conditions\Controller\Adminhtml\ProductCustomOptions\ChooserGrid::URL_PATH,
            ['_current' => true, 'current_grid_id' => $this->getId()]
        );
    }
}
