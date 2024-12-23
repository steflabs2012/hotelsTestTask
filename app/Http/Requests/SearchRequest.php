<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'from'           => 'required|date|before_or_equal:to',
            'to'             => 'required|date|after_or_equal:from',
            'hotel_id'       => 'nullable|integer',
            'region_id'      => 'nullable|integer',
            'main_region_id' => 'nullable|integer',
            'adults'         => 'nullable|integer|min:1|max:10',
            'childrens'      => 'nullable|integer|min:0|max:4',
        ];
    }

    public function messages(): array
    {
        return [
            'from.required'        => 'Обязательно укажите дату заезда.',
            'to.required'          => 'Обязательно укажите дату выезда.',
            'from.before_or_equal' => 'Дата заезда не может быть позже даты выезда.',
            'to.after_or_equal'    => 'Дата выезда не может быть раньше даты заезда.',
            'hotel_id.exists'      => 'Указанный отель не существует.',
            'region_id.exists'     => 'Указанный регион не существует.',
            'adults.min'           => 'Количество взрослых должно быть минимум 1.',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'adults' => $this->input('adults', 1),
        ]);
    }
}
