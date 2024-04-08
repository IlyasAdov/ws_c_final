@extends('layouts.sidebar')

@section('main')
    <div>
        <h5>Изменить платежную квоту</h5>
        <form method="post">
            @csrf
            <div>
                <label for="limit">Лимит:</label><br>
                $<input type="number" name="limit" id="limit" value="{{ old('limit' || $limit) }}"><br>
                @if ($errors->has('limit'))
                    <p>{{ $errors->first('limit') }}</p>
                @endif
            </div><br>
            <button type="submit">Изменить</button>
        </form>
    </div>
@endsection