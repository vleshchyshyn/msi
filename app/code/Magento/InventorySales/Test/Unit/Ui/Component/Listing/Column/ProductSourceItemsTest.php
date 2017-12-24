<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\InventorySales\Test\Unit\Ui\Component\Listing\Column;

use PHPUnit\Framework\TestCase;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Magento\InventoryApi\Api\Data\SourceItemSearchResultsInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\Data\SourceInterface;

use Magento\InventorySales\Ui\Component\Listing\Column\ProductSourceItems;

class ProductSourceItemsTest extends TestCase
{
    /**
     * @var ProductSourceItems
     */
    protected $productSourceItems;

    /**
     * @var SourceItemRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sourceItemRepositoryInterface;

    /**
     * @var SourceItemSearchResultsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sourceItemSearchResultInterface;

    /**
     * @var  SourceRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sourceRepositoryInterface;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchCriteriaBuilderMock;

    /**
     * @var SourceItemInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sourceItemInterfaceMock;

    /**
     * @var SourceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sourceInterfaceMock;

    /**
     * @var ContextInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextInterfaceMock;

    /**
     * @var UiComponentFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $uiComponentFactory;

    public function setUp()
    {
        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->sourceItemInterfaceMock = $this->getMockBuilder(SourceItemInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['create', 'addFilter'])
            ->getMock();
        $this->sourceInterfaceMock = $this->getMockBuilder(SourceInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->sourceItemSearchResultInterface = $this->getMockBuilder(
            SourceItemSearchResultsInterface::class
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextInterfaceMock = $this->getMockBuilder(ContextInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->uiComponentFactory = $this->getMockBuilder(UiComponentFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->sourceItemRepositoryInterface = $this->getMockBuilder(
            SourceItemRepositoryInterface::class
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->sourceRepositoryInterface = $this->getMockBuilder(
            SourceRepositoryInterface::class
        )
            ->disableOriginalConstructor()
            ->getMock();


    }

    public function testHasDataSourceItemsCorrectType()
    {
        $dataSource = [
            'data' => [
                'totalRecords' => 1,
                'items' => [
                    0 => [
                        'entity_id' => 1,
                        'attribute_set_id' => 4,
                        'type_id' => 'simple',
                        'sku' => 'product-test-1',
                        'has_options' => 0,
                        'required_options' => 0,
                        'created_at' => '2017 - 12 - 14 06:59:44',
                        'updated_at' => '2017 - 12 - 14 07:05:57',
                        'qty' => '170.0000',
                        'name' => 'Product test 1',
                        'meta_title' => 'Product test 1',
                        'meta_description' => 'Product test 1',
                        'country_of_manufacture' => 'UA',
                        'url_key' => 'product-test-1',
                        'gift_message_available' => 2,
                        'price' => '165.97',
                        'special_price' => '',
                        'weight' => '15.0000',
                        'status' => 1,
                        'visibility' => 4,
                        'tax_class_id' => 2,
                        'meta_keyword' => 'Product test 1',
                        'websites' => 'Main Website',
                    ]
                ]
            ]
        ];

        $sourceItem1 = clone $this->sourceItemInterfaceMock;
        $sourceItem1->expects($this->once())
            ->method('getSourceId')
            ->willReturn(1);
        $sourceItem1->expects($this->once())
            ->method('getQuantity')
            ->willReturn(10);

        $sourceItem2 = clone $this->sourceItemInterfaceMock;
        $sourceItem2->expects($this->once())
            ->method('getSourceId')
            ->willReturn(2);
        $sourceItem2->expects($this->once())
            ->method('getQuantity')
            ->willReturn(5);

        $sourceItemItems = [
            0 => $sourceItem1,
            1 => $sourceItem2
        ];

        $this->sourceItemSearchResultInterface->expects($this->once())
            ->method('getItems')
            ->willReturn($sourceItemItems);

        $this->sourceItemRepositoryInterface->expects($this->once())
            ->method('getList')
            ->with($this->searchCriteriaBuilderMock)
            ->willReturn($this->sourceItemSearchResultInterface);

        $this->sourceInterfaceMock->expects($this->exactly(2))
            ->method('getName');
        $source1 = clone $this->sourceInterfaceMock;
        $source1->method('getName')->willReturn('Source 1');
        $source2 = clone $this->sourceInterfaceMock;
        $source2->method('getName')->willReturn('Source 2');

        $this->sourceRepositoryInterface->expects($this->exactly(2))
            ->method('get');
        $this->sourceRepositoryInterface->method('get')
            ->with($sourceItem1->getSourceId())
            ->willReturn($source1);
        $this->sourceRepositoryInterface->method('get')
            ->with($sourceItem2->getSourceId())
            ->willReturn($source2);


        $this->sourceItemInterfaceMock->expects($this->once())
            ->method('addFilter')
            ->with(['sku', $dataSource['data']['items'][0]['sku']])
            ->willReturnSelf();

        $objectManager = new ObjectManager($this);
        $this->productSourceItems = $objectManager->getObject(
            ProductSourceItems::class,
            [
                'context' => $this->contextInterfaceMock,
                'uiComponentFactory' => $this->uiComponentFactory,
                'sourceItemRepository' => $this->sourceItemRepositoryInterface,
                'sourceRepository' => $this->sourceRepositoryInterface,
                'searchCriteria' => $this->searchCriteriaBuilderMock,
                'components' => [],
                'data' => []
            ]
        );

        $preparedDataSource = $this->productSourceItems->prepareDataSource($dataSource);

        $this->assertTrue(is_array($preparedDataSource));
        $this->assertTrue(is_array($preparedDataSource['data']));
        $this->assertTrue(is_array($preparedDataSource['data']['items']));
        $this->assertEquals(
            $dataSource['data']['totalRecords'],
            $preparedDataSource['data']['totalRecords']
        );
    }
}
