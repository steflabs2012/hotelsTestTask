<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\PricingPeriod;
use App\Models\Region;
use App\Models\RegionsMain;
use App\Models\RegionsSub;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\Board;
use Carbon\Carbon;
use Exception;


class DataImporter
{
    protected ApiClient $apiClient;

    /**
     * @throws Exception
     */
    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @throws Exception
     */
    public function importHotels(): void
    {
        $this->apiClient->login();

        $hotelsData = $this->apiClient->getActiveHotels();

        foreach ($hotelsData as $hotelData) {
            $hotel = Hotel::query()->firstOrCreate(
                ['id' => $hotelData['Id']],
                [
                    'id'             => $hotelData['Id'],
                    'api_id'         => $hotelData['$id'] ?? 0,
                    'code'           => $hotelData['Code'] ?? '',
                    'remark'         => $hotelData['Remark'] ?? '',
                    'region_id'      => $hotelData['RegionId'] ?? 0,
                    'main_region_id' => $hotelData['MainRegionId'] ?? 0,
                    'category_id'    => $hotelData['CategoryId'] ?? 0,
                    'address'        => $hotelData['Address'] ?? '',
                ]
            );

            $this->importRoomTypes($hotel, $hotelData['RoomTypes']);
        }
    }


    /**
     * @return void
     * @throws Exception
     */
    public function importRegions(): void
    {
        $this->apiClient->login();

        $regionsData = $this->apiClient->getRegions();

        foreach ($regionsData as $regionData) {
            Region::query()->firstOrCreate(
                ['id' => $regionData['Id']],
                [
                    'id'             => $regionData['Id'],
                    'code'           => $regionData['Code'] ?? '',
                    'remark'         => $regionData['Remark'] ?? '',
                    'main_region_id' => $regionData['MainRegionId'] ?? 0,
                    'sub_region_id'  => $regionData['SubRegionId'] ?? 0,
                ]
            );
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function importPricingPeriods(): void
    {
        $this->apiClient->login();

        $hotelIds = Hotel::pluck('id')->toArray();

        foreach ($hotelIds as $hotelId) {

            $contractsData = $this->apiClient->getContracts([
                "HotelId"  => $hotelId,
                "IsActive" => true,
            ]);

            print_r($hotelId . " importded\n");

            $this->importHotelPricingPeriod($contractsData);
        }
    }

    /**
     * @param array $contractsData
     *
     * @return void
     */
    protected function importHotelPricingPeriod(array $contractsData): void
    {
        foreach ($contractsData as $contract) {
            $currency = $this->getCurrencyCode($contract['CurrencyId']);
            $boardId = $contract['Boards'][0] ?? null;

            foreach ($contract['Accommodations'] as $accommodation) {
                PricingPeriod::query()->firstOrCreate(
                    [
                        'hotel_id'     => $contract['HotelId'],
                        'room_type_id' => $accommodation['RoomTypeId'],
                        'board_id'     => $boardId,
                        'from'         => Carbon::parse($accommodation['PeriodBegin'])->toDateString(),
                        'to'           => Carbon::parse($accommodation['PeriodEnd'])->toDateString(),
                        'adults'       => $accommodation['Adult'],
                        'children'     => $accommodation['Child'],
                    ],
                    [
                        'price'    => $accommodation['Price'],
                        'currency' => $currency,
                    ]
                );
            }
        }
    }


    /**
     * @return void
     * @throws Exception
     */
    public function importRegionsMain(): void
    {
        $mainRegionsData = $this->apiClient->getMainRegions();

        foreach ($mainRegionsData as $mainRegionData) {
            RegionsMain::query()->firstOrCreate(
                ['id' => $mainRegionData['Id']],
                [
                    'id'      => $mainRegionData['Id'],
                    'code'    => $mainRegionData['Code'] ?? '',
                    'remark'  => $mainRegionData['Remark'] ?? '',
                    'country' => $mainRegionData['Country'] ?? '',
                ]
            );
        }
    }


    /**
     * @return void
     * @throws Exception
     */
    public function importRegionsSub(): void
    {
        $subRegionsData = $this->apiClient->getSubRegions();

        foreach ($subRegionsData as $subRegionData) {
            RegionsSub::query()->firstOrCreate(
                ['id' => $subRegionData['Id']],
                [
                    'id'             => $subRegionData['Id'],
                    'code'           => $subRegionData['Code'] ?? '',
                    'remark'         => $subRegionData['Remark'] ?? '',
                    'main_region_id' => $subRegionData['MainRegionId'] ?? 0,
                ]
            );
        }
    }

    /**
     * @param Hotel $hotel
     * @param array $roomTypesData
     *
     * @return void
     */
    protected function importRoomTypes(Hotel $hotel, array $roomTypesData): void
    {
        foreach ($roomTypesData as $roomTypeData) {
            $roomType = RoomType::query()->firstOrCreate(
                ['id' => $roomTypeData['Id']],
                [
                    'id'          => $roomTypeData['Id'],
                    'code'        => $roomTypeData['Code'] ?? '',
                    'remark'      => $roomTypeData['Remark'] ?? '',
                    'quota'       => $roomTypeData['Quota'] ?? 0,
                    'on_request'  => $roomTypeData['OnRequest'] ?? 0,
                    'description' => $roomTypeData['Description'] ?? '',
                ]
            );

            $room = Room::query()->firstOrCreate(
                [
                    'hotel_id'     => $hotel->id,
                    'room_type_id' => $roomType->id,
                ],
                [
                    'hotel_id'       => $hotel->id,
                    'room_type_id'   => $roomType->id,
                    'min_paid_adult' => $roomTypeData['MinPaidAdult'] ?? 0,
                    'max_adult'      => $roomTypeData['MaxAdult'] ?? 0,
                    'max_child_age'  => $roomTypeData['MaxChildAge'] ?? 0,
                ]
            );

            $this->importBoards($roomTypeData['Boards'], $room);
        }
    }

    /**
     * @param array $boardsData
     * @param Room  $room
     *
     * @return void
     */
    protected function importBoards(array $boardsData, Room $room): void
    {
        foreach ($boardsData as $boardData) {
            $board = Board::query()->firstOrCreate(
                ['id' => $boardData['Id']],
                [
                    'id'     => $boardData['Id'],
                    'api_id' => $boardData['$id'] ?? 0,
                    'code'   => $boardData['Code'] ?? '',
                    'remark' => $boardData['Remark'] ?? '',
                ]
            );

            $room->boards()->syncWithoutDetaching([$board->id]);
        }
    }

    /**
     * @param int $currencyId
     *
     * @return string
     */
    protected function getCurrencyCode(int $currencyId): string
    {
        $currencies = [
            43 => 'TL',
            45 => 'USD',
            44 => 'EUR',
            46 => 'RUB',
        ];

        return $currencies[$currencyId] ?? 'UNKNOWN';
    }
}
