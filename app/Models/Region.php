<?php

namespace App\Models;

use App\Traits\HasFormattedName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Region extends Model
{
    use HasFactory;

    use HasFormattedName;

    protected $table = 'regions';

    protected $fillable = [
        'id',
        'code',
        'remark',
        'main_region_id',
        'sub_region_id',
    ];

    /**
     * @return BelongsTo
     */
    public function mainRegion(): BelongsTo
    {
        return $this->belongsTo(RegionsMain::class, 'main_region_id');
    }

    /**
     * @return BelongsTo
     */
    public function subRegion(): BelongsTo
    {
        return $this->belongsTo(RegionsSub::class, 'sub_region_id');
    }
}
