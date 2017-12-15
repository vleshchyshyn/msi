<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\InventorySales\Ui\Component\Listing\Column;

use Magento\Inventory\Model\SourceRepository;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Add grid column for source items. Prepare data
 */
class ProductSourceItems extends Column
{
    /** @var SourceItemRepositoryInterface  */
    protected $sourceItemRepository;

    /** @var  SourceRepository */
    private $sourceRepository;

    /** @var SearchCriteriaBuilder */
    protected $searchCriteria;

    /**
     * ProductSourceItems constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param SourceItemRepositoryInterface $sourceItemRepository
     * @param SourceRepositoryInterface $sourceRepository
     * @param SearchCriteriaBuilder $searchCriteria
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        SourceItemRepositoryInterface $sourceItemRepository,
        SourceRepositoryInterface $sourceRepository,
        SearchCriteriaBuilder $searchCriteria,
        array $components = [],
        array $data = []
    ) {
        $this->sourceItemRepository = $sourceItemRepository;
        $this->sourceRepository = $sourceRepository;
        $this->searchCriteria = $searchCriteria;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare data source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if ($dataSource['data']['totalRecords'] > 0) {
            foreach ($dataSource['data']['items'] as &$row) {
                $row['source_items'] = isset($row['sku'])
                    ? $this->prepareSourceItemsData($row['sku']) : [];
            }
        }
        unset($row);

        return $dataSource;
    }

    /**
     * Prepare sales value
     *
     * @param string $productSku
     *
     * @return array
     */
    private function prepareSourceItemsData(string $productSku): array
    {
        $preparedSourceData = [];
        $searchSourceItemsCriteria = $this->searchCriteria->addFilter('sku', $productSku)->create();
        $productSourceItems = $this->sourceItemRepository->getList($searchSourceItemsCriteria)->getItems();

        if (sizeof($productSourceItems)) {
            /** @var SourceItemInterface $sourceItem */
            foreach ($productSourceItems as $sourceItem) {
                $preparedSourceData[] = [
                    'name' => $this->getSourceItemSource((int)$sourceItem->getSourceId())->getName(),
                    'qty' => (float)$sourceItem->getQuantity()
                ];
            }
        }

        return $preparedSourceData;
    }

    /**
     * Get Source by ID
     *
     * @param int $sourceId
     *
     * @return SourceInterface
     */
    protected function getSourceItemSource(int $sourceId): SourceInterface
    {
        return $this->sourceRepository->get($sourceId);
    }
}
