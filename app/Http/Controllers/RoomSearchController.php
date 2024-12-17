<?php

namespace App\Http\Controllers;


use App\DTO\SearchFilterParams;
use App\Http\Requests\SearchRequest;
use App\Models\Hotel;
use App\Models\Region;
use App\Models\RegionsMain;
use App\Models\Room;
use App\Services\RoomAvailabilityService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Throwable;


class RoomSearchController extends Controller
{
    /**
     * @return Factory|View|Application
     */
    public function index(): Factory|View|Application
    {
        $hotels = Hotel::select('id', 'remark')->get();
        $regions = Region::select('id', 'remark')->get();
        $regionsMain = RegionsMain::select('id', 'remark')->get();
        return view('search', compact('hotels', 'regions', 'regionsMain'));
    }

    /**
     * @param SearchRequest           $request
     * @param RoomAvailabilityService $roomAvailabilityService
     *
     * @return JsonResponse
     */
    public function search(SearchRequest $request, RoomAvailabilityService $roomAvailabilityService): JsonResponse
    {
        $validated = $request->validated();

        $from = $validated['from'];
        $to = $validated['to'];
        $hotelId = $validated['hotel_id'];

        $filterParams = new SearchFilterParams([
            'hotel_id'       => $request->input('hotel_id') ?? null,
            'adults'         => $request->input('adults') ?? 1,
            'region_id'      => $request->input('region_id') ?? null,
            'main_region_id' => $request->input('main_region_id') ?? null,
        ]);

        try {
            $rooms = Room::getByFilterParams($filterParams, ['hotel', 'hotel.region', 'hotel.regionsMain', 'roomType', 'boards']);
            $avalableRooms = $roomAvailabilityService->filterAvailableRooms($rooms, $from, $to, $hotelId);
        } catch (Throwable $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }

        $roomsData = collect();

        foreach ($avalableRooms as $room) {
            $roomsData->push([
                'hotel_id'       => $room->hotel->id,
                'hotel_name'     => $room->hotel->formatted_name,
                'hotel_adress'   => $room->hotel->address,
                'region_id'      => $room->hotel->region_id,
                'region'         => $room->hotel->region->formatted_name,
                'city'           => $room->hotel->regionsMain->formatted_name,
                'main_region_id' => $room->hotel->main_region_id,
                'room_type_id'   => $room->roomType->id,
                'room_type_name' => $room->roomType->formatted_name,
                'room_type_code' => $room->roomType->code,
                'min_adults'     => $room->roomType->min_paid_adult ?? null,
                'max_adults'     => $room->roomType->max_adult ?? null,
                'boards'         => implode(', ', $room->boards->pluck('formatted_name')->toArray()),
            ]);
        }

        return response()->json($roomsData);
    }
}
