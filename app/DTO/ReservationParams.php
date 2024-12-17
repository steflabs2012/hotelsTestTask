<?php

namespace App\DTO;

class ReservationParams
{
    public ?string $recordDateBegin = null;
    public ?string $recordDateEnd   = null;
    public ?int    $hotelId         = null;
    public ?string $updateDateBegin = null;
    public ?string $updateDateEnd   = null;
    public ?int    $recId           = null;
    public ?string $voucherNo       = null;
    public ?string $sourceId        = null;
    public ?int    $confirmStatus   = null;
    public ?string $orderId         = null;

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->recordDateBegin = $params['from_creation'] ?? null;
        $this->recordDateEnd = $params['to_creation'] ?? null;
        $this->hotelId = $params['hotel_id'] ?? null;
        $this->updateDateBegin = $params['from'] ?? null;
        $this->updateDateEnd = $params['to'] ?? null;
        $this->recId = $params['RecId'] ?? null;
        $this->voucherNo = $params['VoucherNo'] ?? null;
        $this->sourceId = $params['SourceId'] ?? null;
        $this->confirmStatus = $params['ConfirmStatus'] ?? null;
        $this->orderId = $params['OrderId'] ?? null;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'RecordDateBegin' => $this->recordDateBegin,
            'RecordDateEnd'   => $this->recordDateEnd,
            'UpdateDateBegin' => $this->updateDateBegin,
            'UpdateDateEnd'   => $this->updateDateEnd,
            'RecId'           => $this->recId,
            'VoucherNo'       => $this->voucherNo,
            'SourceId'        => $this->sourceId,
            'ConfirmStatus'   => $this->confirmStatus,
            'HotelId'         => $this->hotelId,
            'OrderId'         => $this->orderId,
        ], function ($value) {
            return !is_null($value) && $value !== '';
        });
    }
}
