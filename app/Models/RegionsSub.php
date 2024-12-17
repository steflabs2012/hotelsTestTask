<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RegionsSub extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $table = 'regions_sub';

    protected $fillable = [
        'id',
        'code',
        'remark',
        'main_region_id',
    ];

    /**
     * @return BelongsTo
     */
    public function mainRegion(): BelongsTo
    {
        return $this->belongsTo(RegionsMain::class, 'main_region_id');
    }

    /**
     * @return HasMany
     */
    public function regions(): HasMany
    {
        return $this->hasMany(Region::class, 'sub_region_id');
    }
}
