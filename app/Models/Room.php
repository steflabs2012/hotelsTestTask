<?php
namespace App\Models;

use App\DTO\SearchFilterParams;
use Illuminate\Database\Eloquent\Builder;
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

        if ($filterParams->adults) {
            $roomsQuery->whereHas('roomType', function ($query) use ($filterParams) {
                $query->where(function ($q) use ($filterParams) {
                    $q->where('max_adult', '>=', $filterParams->adults)
                      ->where('min_paid_adult', '<=', $filterParams->adults);
                })->orWhere(function ($q) {
                    $q->where('min_paid_adult', 0)
                      ->where('max_adult', 0);
                });
            });
        }

        return $roomsQuery->with($with)->get();
    }
}
