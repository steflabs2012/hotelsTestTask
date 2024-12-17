<?php
namespace App\Models;

use App\Traits\HasFormattedName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    use HasFactory;

    use HasFormattedName;

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'api_id',
        'code',
        'remark',
        'region_id',
        'main_region_id',
        'category_id',
        'address',
    ];

    /**
     * @return HasMany
     */
    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class);
    }

    /**
     * @return BelongsTo
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * @return BelongsTo
     */
    public function regionsMain(): BelongsTo
    {
        return $this->belongsTo(RegionsMain::class, 'main_region_id');
    }

    /**
     * @return HasMany
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }


}
