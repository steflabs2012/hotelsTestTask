<?php

namespace App\DTO;

class SearchFilterParams
{
    public ?int $hotelId   = null;
    public ?int $adults    = null;
    public ?int $childrens = null;
    public ?int $cityId    = null;
    public ?int $regionId  = null;

    public string $from;

    public string $to;

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->hotelId = $params['hotel_id'] ?? null;
        $this->adults = $params['adults'] ?? null;
        $this->childrens = $params['childrens'] ?? null;
        $this->cityId = $params['main_region_id'] ?? null;
        $this->regionId = $params['region_id'] ?? null;
        $this->from = $params['from'] ?? null;
        $this->to = $params['to'] ?? null;
    }

    /**
     * @return bool
     */
    public function hasFilters(): bool
    {
        return $this->hotelId || $this->adults || $this->cityId || $this->regionId;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'hotel_id'       => $this->hotelId,
            'adults'         => $this->adults,
            'childrens'      => $this->childrens,
            'main_region_id' => $this->cityId,
            'region_id'      => $this->regionId,
            'from'           => $this->from,
            'to'             => $this->to,
        ], function ($value) {
            return !is_null($value) && $value !== '';
        });
    }
}
