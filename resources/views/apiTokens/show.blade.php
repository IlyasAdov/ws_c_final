@extends('layouts.sidebar')

@section('main')
    <div>
        <h5>API Token '{{ $apiToken->name }}'</h5>
        <p>Новый секретный токен сгенерирован:</p>
        <p>{{ $apiToken->token }}</p>
        <p>Обязательно сохраните токен, так как это будет единственный раз, когда его можно будет посмотреть.</p>
        <a href="{{ route('workspaces.show', ['workspaceId' => $workspace->id]) }}">Вернуться в рабочую область</a>
    </div>
@endsection