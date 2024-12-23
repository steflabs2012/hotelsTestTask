<?php
namespace App\Models;

use App\Traits\HasFormattedName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    use HasFactory;

    use HasFormattedName;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'hotel_id',
        'code',
        'remark',
        'quota',
        'on_request',
        'description',
    ];

    /**
     * @return BelongsTo
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * @return HasMany
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
