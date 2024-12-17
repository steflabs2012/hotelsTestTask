<?php

namespace App\DTO;

class SearchFilterParams
{
    public ?int $hotelId  = null;
    public ?int $adults   = null;
    public ?int $cityId   = null;
    public ?int $regionId = null;

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->hotelId = $params['hotel_id'] ?? null;
        $this->adults = $params['adults'] ?? null;
        $this->cityId = $params['main_region_id'] ?? null;
        $this->regionId = $params['region_id'] ?? null;
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
            'hotel_id'     => $this->hotelId,
            'adults_count' => $this->adults,
            'city_code'    => $this->cityId,
            'country_code' => $this->regionId,
        ], function ($value) {
            return !is_null($value) && $value !== '';
        });
    }
}
