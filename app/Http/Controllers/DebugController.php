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
            "from" => "2024-10-15 00:00:00",
            "to_creation" => "2024-10-27 00:00:00",
            "hotel_id" => 1791

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
}
