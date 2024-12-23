<?php

namespace App\Http\Controllers;


use App\DTO\SearchFilterParams;
use App\Http\Requests\SearchRequest;
use App\Models\Hotel;
use App\Models\Region;
use App\Models\RegionsMain;
use App\Models\Room;
use App\Services\RoomAvailabilityService;
use Exception;
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
     * @throws Exception
     */
    public function search(SearchRequest $request, RoomAvailabilityService $roomAvailabilityService): JsonResponse
    {
        $validated = $request->validated();

        $filterParams = new SearchFilterParams([
            'hotel_id'       => $validated['hotel_id'] ?? null,
            'adults'         => $validated['adults'] ?? 1,
            'childrens'      => $validated['childrens'] ?? 0,
            'region_id'      => $request->input('region_id'),
            'main_region_id' => $request->input('main_region_id'),
            'from'           => $validated['from'],
            'to'             => $validated['to'],
        ]);

        $avalableRooms = $roomAvailabilityService->getSearchData($filterParams);

        $roomsData = collect();

        foreach ($avalableRooms as $room) {

            $pricesHtml = view('partials.room.price', [
                'preparedPeriods' => $room->preparedPeriods,
                'adults'          => $filterParams->adults,
                'childrens'       => $filterParams->childrens,
            ])->render();

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
                'min_adults'     => $room->min_paid_adult ?? null,
                'max_adults'     => $room->max_adult ?? null,
                'boards'         => implode(', ', $room->boards->pluck('formatted_name')->toArray()),
                'prices_html'    => $pricesHtml,
            ]);
        }

        return response()->json($roomsData);
    }
}
