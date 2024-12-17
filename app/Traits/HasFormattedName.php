<?php
namespace App\Traits;

trait HasFormattedName
{
    public function getFormattedNameAttribute()
    {
        return mb_convert_case(mb_strtolower($this->remark), MB_CASE_TITLE, "UTF-8");
    }
}
