<?php

namespace App\Models;

use App\DTO\SearchFilterParams;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'room_type_id',
        'min_paid_adult',
        'max_adult',
        'max_child_age',
    ];

    /**
     * @return BelongsTo
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * @return BelongsTo
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * @return BelongsToMany
     */
    public function boards(): BelongsToMany
    {
        return $this->belongsToMany(Board::class, 'room_board');
    }

    public function pricingPeriods()
    {
        return $this->hasMany(PricingPeriod::class, 'room_type_id', 'room_type_id');
    }
    /**
     * @param SearchFilterParams $filterParams
     * @param array              $with
     *
     * @return Collection|\Illuminate\Support\Collection
     */
    public static function getByFilterParams(SearchFilterParams $filterParams, array $with = []): Collection|\Illuminate\Support\Collection
    {
        $roomsQuery = Room::query();

        if ($filterParams->hotelId) {
            $roomsQuery->where('hotel_id', $filterParams->hotelId);
        }

        if ($filterParams->cityId || $filterParams->regionId) {
            $roomsQuery->whereHas('hotel', function ($query) use ($filterParams) {
                if ($filterParams->cityId) {
                    $query->where('main_region_id', $filterParams->cityId);
                }
                if ($filterParams->regionId) {
                    $query->where('region_id', $filterParams->regionId);
                }
            });
        }

        $roomsQuery->whereHas('pricingPeriods', function ($query) use ($filterParams) {
            self::applyPricingPeriodsFilter($query, $filterParams);
        });

        //todo collect only fields that we really need
        $rooms = $roomsQuery->with(array_merge($with, [
            'pricingPeriods' => function ($query) use ($filterParams) {
                self::applyPricingPeriodsFilter($query, $filterParams);
            },
        ]))->get();

        return $rooms;
    }

    /**
     * @param                    $query
     * @param SearchFilterParams $filterParams
     *
     * @return void
     */
    private static function applyPricingPeriodsFilter($query, SearchFilterParams $filterParams): void
    {
        $query->where('hotel_id', $filterParams->hotelId);

        if ($filterParams->from && $filterParams->to) {
            $query->where('from', '<=', $filterParams->to)
                  ->where('to', '>=', $filterParams->from);
        }
        if ($filterParams->adults) {
            $query->where('adults', '=', $filterParams->adults);
        }
        if ($filterParams->childrens !== null) {
            $query->where('children', '=', $filterParams->childrens);
        }
    }

    /**
     * @param $from
     * @param $to
     *
     * @return array
     */
    public function preparePricingPeriod($from, $to): array
    {
        $pricingPeriods = $this->pricingPeriods->toArray();

        $result = [];

        // Преобразуем пользовательские даты в объекты Carbon
        $userFrom = Carbon::parse($from);
        $userTo = Carbon::parse($to);

        // Собираем «граничные» даты
        // Берём userFrom, а для userTo и периодов - добавим +1 день (чтобы избежать дубли)
        $dates = [$userFrom->clone()];

        foreach ($pricingPeriods as $period) {
            $periodFrom = Carbon::parse($period['from']);
            $periodTo = Carbon::parse($period['to']);

            // Если период совсем не пересекается с [userFrom, userTo], пропускаем
            if ($periodTo < $userFrom || $periodFrom > $userTo) {
                continue;
            }

            // Берём границы с учётом пользовательского диапазона
            $start = $periodFrom->greaterThan($userFrom) ? $periodFrom : $userFrom;
            $end = $periodTo->lessThan($userTo) ? $periodTo : $userTo;

            // Начало периода добавляем как есть
            $dates[] = $start->clone();
            // Конец периода +1 день (для «верхней» границы)
            $dates[] = $end->clone()->addDay();
        }

        // Добавляем «последнюю» границу +1 день
        $dates[] = $userTo->clone()->addDay();

        // Убираем дубли и сортируем
        $dates = array_unique(array_map(fn($d) => $d->toDateString(), $dates));
        sort($dates);

        // Формируем интервалы между соседними датами
        for ($i = 0; $i < count($dates) - 1; $i++) {
            $currentFrom = Carbon::parse($dates[$i]);
            $currentTo = Carbon::parse($dates[$i + 1])->subDay();

            // Если вышло, что currentFrom > currentTo - пропускаем
            if ($currentFrom->gt($currentTo)) {
                continue;
            }

            // Находим подходящие pricing_periods для этого отрезка
            $periodsInRange = array_filter($pricingPeriods, function ($period) use ($currentFrom, $currentTo) {
                $pFrom = Carbon::parse($period['from']);
                $pTo = Carbon::parse($period['to']);
                return $pFrom <= $currentTo && $pTo >= $currentFrom;
            });

            if (empty($periodsInRange)) {
                continue;
            }

            // Группируем по board_id
            $groupedByBoard = [];
            $daysInPeriod = $currentFrom->diffInDays($currentTo) + 1;

            foreach ($periodsInRange as $period) {
                $boardId = $period['board_id'];

                $boardName = optional($this->boards->where('id', $boardId)->first())->remark ?? null;
                if (!$boardName) {
                    $boardName = optional(Board::where('id', $boardId)->first())->remark ?? null;
                }
                $groupedByBoard[$boardId] = [
                    'board_name'  => $boardName,
                    'price'       => $period['price'],
                    'price_total' => $period['price'] * $daysInPeriod,
                    'days'        => $daysInPeriod,
                    'currency'    => $period['currency'],
                    'adults'      => $period['adults'],
                    'children'    => $period['children'],
                ];
            }

            $key = $currentFrom->toDateString() . ' - ' . $currentTo->toDateString();
            $result[$key] = $groupedByBoard;
        }

        return $result;
    }
}
