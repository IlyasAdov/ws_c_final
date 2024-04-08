@extends('layouts.sidebar')

@section('main')
    <div>
        <h4>Workspaces</h4>
        <a href="{{ route('workspaces.create') }}">Create new Workspace</a>
    </div>

    @if (empty($workspaces) || count($workspaces) === 0)
        <p>Рабочие области отсутсвуют.</p>
    @else
        @foreach ($workspaces as $workspace)
            <div>
                <h5>{{ $workspace->title }}</h5>
                <p>{{ $workspace->description }}</p>
                <a href="{{ route('workspaces.edit', ['workspaceId' => $workspace->id]) }}">Edit</a>
                <a href="{{ route('workspaces.show', ['workspaceId' => $workspace->id]) }}">Open</a>
            </div>
        @endforeach
    @endif
@endsection