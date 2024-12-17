<?php

namespace App\Services;

use App\DTO\ReservationParams;
use App\Services\ApiClient as Api;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;


class RoomAvailabilityService
{
    protected Api $apiClient;

    /**
     * @throws Exception
     */
    public function __construct(Api $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @param Collection $rooms
     * @param string     $from
     * @param string     $to
     * @param int|null   $hotel_id
     *
     * @return Collection
     * @throws Exception
     */
    public function filterAvailableRooms(Collection $rooms, string $from, string $to, ?int $hotel_id = null): Collection
    {
        $this->apiClient->login();

        $reservedRoomsByHotelsIds = $this->getReservedRoomsByHotelsIds($from, $to, $hotel_id);

        $avalableRooms = $rooms->filter(function ($room) use ($reservedRoomsByHotelsIds) {
            if (isset($reservedRoomsByHotelsIds[$room->hotel_id])) {
                return !in_array($room->room_type_id, $reservedRoomsByHotelsIds[$room->hotel_id]);
            }
            return true;
        });

        return $avalableRooms;
    }

    protected function getReservedRoomsByHotelsIds(string $from, string $to, ?int $hotel_id = null): array
    {
        $reservionParams = new ReservationParams([
            'from'        => $from,
            'to_creation' => $to,
            'hotel_id'    => $hotel_id,
        ]);

        $reservations = $this->apiClient->getReservations($reservionParams);

        $reservedRoomIdsByHotel = $this->extractReservedRoomIdsByHotel($reservations, $from, $to);

        return $reservedRoomIdsByHotel;
    }

    /**
     * @param array $reservations
     * @param       $from
     * @param       $to
     *
     * @return array
     */
    protected function extractReservedRoomIdsByHotel(array $reservations, $from, $to): array
    {
        $reservedRoomsByHotel = [];
        $requestFrom = Carbon::parse($from);
        $requestTo = Carbon::parse($to);

        foreach ($reservations as $reservation) {
            $beginDate = Carbon::parse($reservation['BeginDate']);
            $endDate = Carbon::parse($reservation['EndDate']);

            if ($beginDate <= $requestTo && $endDate >= $requestFrom) {
                if (!empty($reservation['ReservationDetails'][0])) {
                    $hotelId = $reservation['ReservationDetails'][0]['HotelId'];
                    $roomTypeId = $reservation['ReservationDetails'][0]['RoomTypeId'];

                    if (!isset($reservedRoomsByHotel[$hotelId])) {
                        $reservedRoomsByHotel[$hotelId] = [];
                    }
                    $reservedRoomsByHotel[$hotelId][] = $roomTypeId;
                    $reservedRoomsByHotel[$hotelId] = array_unique($reservedRoomsByHotel[$hotelId]);
                }
            }
        }

        return $reservedRoomsByHotel;
    }
}
