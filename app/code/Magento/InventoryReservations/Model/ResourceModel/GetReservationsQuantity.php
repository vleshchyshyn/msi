<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\InventoryReservations\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\InventoryReservationsApi\Api\Data\ReservationInterface;
use Magento\InventoryReservations\Model\GetReservationsQuantityInterface;
use Magento\InventoryReservations\Setup\Operation\CreateReservationTable;

/**
 * @inheritdoc
 */
class GetReservationsQuantity implements GetReservationsQuantityInterface
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $sku, int $stockId): float
    {
        $connection = $this->resource->getConnection();
        $reservationTable = $this->resource->getTableName(CreateReservationTable::TABLE_NAME_RESERVATION);

        $select = $connection->select()
            ->from($reservationTable, [ReservationInterface::QUANTITY => 'SUM(' . ReservationInterface::QUANTITY . ')'])
            ->where(ReservationInterface::SKU . ' = ?', $sku)
            ->where(ReservationInterface::STOCK_ID . ' = ?', $stockId)
            ->limit(1);

        $reservationQty = $connection->fetchOne($select);
        if (false === $reservationQty) {
            $reservationQty = 0;
        }
        return (float)$reservationQty;
    }
}
