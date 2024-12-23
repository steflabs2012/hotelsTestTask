@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Поиск свободных комнат</h1>

        <!-- Ошибки валидации -->
        <div id="validationErrors" class="alert alert-danger d-none">
            <ul id="errorList"></ul>
        </div>

        <form id="searchForm" class="mb-4">
            <div class="row">
                <div class="col-md-2">
                    <label for="hotel" class="form-label">Отель</label>

                    <select id="hotel" name="hotel_id" class="form-select">
                        <option value="">Не указывать</option>
                        @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}">{{ $hotel->id }} - {{ $hotel->formatted_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Поле выбора города и страны -->
                <div class="col-md-2">
                    <label for="location" class="form-label">Город</label>
                    <select id="main_region" name="main_region_id" class="form-select">
                        <option value="">Не указывать</option>
                        @foreach($regionsMain as $regionMain)
                            <option value="{{ $regionMain->id }}">{{ $regionMain->id }} - {{ $regionMain->formatted_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="location" class="form-label">Регион</label>
                    <select id="region" name="region_id" class="form-select">
                        <option value="">Не указывать</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}">{{ $region->id }} - {{ $region->formatted_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Поля выбора дат -->
                <div class="col-md-2">
                    <label for="from" class="form-label">Дата заезда</label>
                    <input type="date" id="from" name="from" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="to" class="form-label">Дата выезда</label>
                    <input type="date" id="to" name="to" class="form-control">
                </div>

                <!-- Поле выбора количества взрослых -->
                <div class="col-md-1">
                    <label for="adults" class="form-label">Взрослых</label>
                    <select id="adults" name="adults" class="form-select">
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <!-- Поле выбора количества детей -->
                <div class="col-md-1">
                    <label for="childrens" class="form-label">Детей</label>
                    <select id="childrens" name="childrens" class="form-select">
                        @for($i = 0; $i <= 4; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="mt-3">
                <button type="button" id="searchButton" class="btn btn-primary">Найти</button>
            </div>
        </form>

        <!-- Таблица с результатами -->
        <h2>Результаты поиска</h2>
        <table class="table table-bordered" id="resultsTable">
            <thead>
            <tr>
                <th>Отель</th>
                <th>Город</th>
                <th>Регион</th>
                <th>Тип комнаты</th>
                <th>Типы обслуживания</th>
                <th>Макс. взрослых</th>
                <th>Цены</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="7" class="text-center">Результатов пока нет</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
@push('scripts')
<script>
    $(document).ready(function () {
        $('#searchButton').on('click', function () {

            $('#validationErrors').addClass('d-none');
            $('#errorList').empty();

            const formData = {
                hotel_id: $('#hotel').val(),
                location: $('#location').val(),
                from: $('#from').val(),
                to: $('#to').val(),
                adults: $('#adults').val(),
                childrens: $('#childrens').val(),
                region_id: $('#region').val(),
                main_region_id: $('#main_region').val(),
            };

            $.ajax({
                url: '{{ route("search.rooms") }}',
                method: 'GET',
                data: formData,
                success: function (response) {

                    const tableBody = $('#resultsTable tbody');
                    tableBody.empty();

                    if (response.length === 0) {
                        tableBody.append('<tr><td colspan="7" class="text-center">Нет доступных комнат</td></tr>');
                    } else {
                        response.forEach(function (room) {
                            tableBody.append(`
                            <tr>
                                <td>${room.hotel_id} - ${room.hotel_name}</td>
                                <td>${room.city}</td>
                                <td>${room.region}</td>
                                <td>${room.room_type_id} - ${room.room_type_name} ${room.room_type_code}</td>
                                <td>${room.boards}</td>
                                <td>${room.min_adults} - ${room.max_adults}</td>
                                <td>${room.prices_html}</td>
                            </tr>
                        `);
                        });
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {

                        const errors = xhr.responseJSON.errors;
                        $('#validationErrors').removeClass('d-none');

                        for (const [field, messages] of Object.entries(errors)) {
                            messages.forEach(function (message) {
                                $('#errorList').append(`<li>${message}</li>`);
                            });
                        }
                    } else {
                        alert('Ошибка при выполнении поиска.');
                    }
                },
            });
        });
    });
</script>
@endpush
