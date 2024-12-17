<?php

namespace App\Models;

use App\Traits\HasFormattedName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RegionsMain extends Model
{
    use HasFactory;

    use HasFormattedName;

    public $incrementing = false;

    protected $table = 'regions_main';

    protected $fillable = [
        'id',
        'code',
        'remark',
        'country',
    ];

    /**
     * @return HasMany
     */
    public function subregions(): HasMany
    {
        return $this->hasMany(RegionsSub::class, 'main_region_id');
    }

    /**
     * @return HasMany
     */
    public function regions(): HasMany
    {
        return $this->hasMany(Region::class, 'main_region_id');
    }
}
