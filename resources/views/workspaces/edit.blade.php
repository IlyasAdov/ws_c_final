@extends('layouts.sidebar')

@section('main')
    <div>
        <h5>Edit Workspace '{{ $workspace->title }}'</h5>
        <form action="{{ route('workspaces.update', ['workspaceId' => $workspace->id]) }}" method="post">
            @csrf
            <div>
                <label for="title">Title:</label><br>
                <input type="text" name="title" id="title" value="{{ old('title', $workspace->title) }}" required><br>
                @error('title')
                    <p>{{ $message }}</p><br>
                @enderror
            </div><br>
            <div>
                <label for="description">Description:</label><br>
                <textarea name="description" id="description" cols="30" rows="10">{{ old('description', $workspace->description) }}</textarea><br>
                @error('description')
                    <p>{{ $message }}</p><br>
                @enderror
            </div><br>

            <button type="submit">Update</button>
        </form>
    </div>
@endsection