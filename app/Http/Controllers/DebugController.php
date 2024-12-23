<?php

namespace App\Http\Controllers;

use App\DTO\ReservationParams;
use App\Models\Room;
use App\Services\ApiClient;
use App\Services\RoomAvailabilityService;
use Exception;

class DebugController extends Controller
{
    public function sync(ApiClient $apiClient, RoomAvailabilityService $roomAvailabilityService)
    {
        $hotelId = 1791;
        $from = "2024-09-07 00:00:00";
        $to = "2024-10-15 00:00:00";

        //        $roomAvailabilityService->getAvailableRooms($from, $to, $hotelId);

        echo '<pre>';
        var_dump($roomAvailabilityService);
        exit;
        echo '</pre>';
    }

    public function regions(ApiClient $apiClient)
    {
        $opId = $apiClient->login();

        $data = $apiClient->getRegions();
        echo '<pre>';
        var_dump($data);
        exit;
        echo '</pre>';
    }

    public function roomtypes(ApiClient $apiClient)
    {
        $opId = $apiClient->login();

        $data = $apiClient->getRoomTypes();
        echo '<pre>';
        var_dump($data);
        exit;
        echo '</pre>';
    }

    /**
     * @throws Exception
     */
    public function mainregions(ApiClient $apiClient)
    {
        $opId = $apiClient->login();

        $data = $apiClient->getMainRegions();
        echo '<pre>';
        var_dump($data);
        exit;
        echo '</pre>';
    }

    /**
     * @throws Exception
     */
    public function subregions(ApiClient $apiClient)
    {
        $opId = $apiClient->login();

        $data = $apiClient->getSubRegions();
        echo '<pre>';
        var_dump($data);
        exit;
        echo '</pre>';
    }

    /**
     * @throws Exception
     */
    public function reservations(ApiClient $apiClient)
    {
        $opId = $apiClient->login();

        $params = new ReservationParams([
            "from"        => "2024-10-15 00:00:00",
            "to_creation" => "2024-10-27 00:00:00",
            "hotel_id"    => 1791

        ]);

        $data = $apiClient->getReservations($params);
        echo '<pre>';
        var_dump($data);
        exit;
        echo '</pre>';
    }

    public function activehotels(ApiClient $apiClient)
    {
        $opId = $apiClient->login();

        $data = $apiClient->getActiveHotels();
        echo '<pre>';
        echo var_dump($data);
        //        echo json_encode($data);
        exit;
        echo '</pre>';
    }

    /**
     * @throws Exception
     */
    public function allhotels(ApiClient $apiClient)
    {
        $opId = $apiClient->login();

        $data = $apiClient->getAllHotels();
        echo '<pre>';
        var_dump($data);
        exit;
        echo '</pre>';
    }

    public function currencyList(ApiClient $apiClient)
    {
        $opId = $apiClient->login();

        $data = $apiClient->getCurrencyList([]);

        echo '<pre>';
        var_dump($data);
        exit;
        echo '</pre>';
    }

    public function priceSearch(ApiClient $apiClient)
    {
        $test = new ApiClient();
        $opId = $test->login();

        $data = $test->priceSearch([
            "OperatorId" => $opId,
            //            "RegionId" => 2,
            "BeginDate"  => "2021-12-01 00:00:00",
            "EndDate"    => "2022-12-30 00:00:00",
            "HotelId"    => 693,
            //            "IsAvailable" => true,
            //            "Pax" => 6,
            //            "Childs" => 7,
            //            "ChildInfo" => [1, 2],
            //            "RemainderQuotaCheck" => true,
            //            "SaleDate" => "2021-10-20 00:00:00",

            //            "WithoutInformation" => true,
            //            "RoomTypeId" => 12,
            //            "MainRegionId" => 13,
            //            "SubregionId" => 14,
            //            "BoardCode" => "sample string 15",
            //            "HotelList" => [1, 2],
        ]);

        echo '<pre>';
        var_dump($data);
        exit;
        echo '</pre>';
    }

    public function getContracts(ApiClient $apiClient)
    {
        $opId = $apiClient->login();

        $data = $apiClient->getContracts([
            //            "RecId" => 1,
            "HotelId"  => 1060,
            //            "RegionId" => 3,
            //            "CategoryId" => 4,
            //            "CurrencyId" => 5,
            "IsActive" => true,
            //            "SeasonId" => 8,
            //            "RecordBegin" => "2024-11-05 00:00:00",
            //            "RecordEnd" => "2024-11-14 00:00:00",
            //            "UpdateBegin" => "2021-10-20T16:27:04.0787379+03:00",
            //            "UpdateEnd" => "2021-10-20T16:27:04.0787379+03:00"
        ]);

        echo '<pre>';
        var_dump($data);
        exit;
        echo '</pre>';
    }

    public function getPackets(ApiClient $apiClient)
    {
        $opId = $apiClient->login();

        $data = $apiClient->getContracts([
            //            "RecId" => 1,
            "HotelId"  => 693,
            //            "RegionId" => 3,
            //            "CategoryId" => 4,
            //            "CurrencyId" => 5,
            "IsActive" => true,
            //            "SeasonId" => 8,
            //            "RecordBegin" => "2021-10-20T16:27:04.0787379+03:00",
            //            "RecordEnd" => "2021-10-20T16:27:04.0787379+03:00",
            //            "UpdateBegin" => "2021-10-20T16:27:04.0787379+03:00",
            //            "UpdateEnd" => "2021-10-20T16:27:04.0787379+03:00"
        ]);

        echo '<pre>';
        var_dump($data);
        exit;
        echo '</pre>';
    }
}
