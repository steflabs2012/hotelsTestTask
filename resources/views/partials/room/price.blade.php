Price information by periods for<br> {{$adults}} adults and {{$childrens}} childrens:
@if(count($preparedPeriods) > 1)
    @foreach($preparedPeriods as $period => $boards)
        <div style="margin-bottom: 10px;">
            <strong>{{ $period }}</strong>
            @foreach($boards as $board)
                <div style="padding-left: 10px;">
                    <span>{{ $board['board_name'] }}</span>:
                    <span>{{ $board['price'] }} {{ $board['currency'] }}</span>
                    (Total: {{ $board['days'] }} days {{ $board['price_total'] }} {{ $board['currency'] }})
                </div>
            @endforeach
        </div>
    @endforeach
@else
    @foreach($preparedPeriods as $boards)
        @foreach($boards as $board)
            <div>
                <span>{{ $board['board_name'] }}</span>:
                <span>{{ $board['price'] }} {{ $board['currency'] }}</span>
                (Total: {{ $board['days'] }} days {{ $board['price_total'] }} {{ $board['currency'] }})
            </div>
        @endforeach
    @endforeach
@endif
