@extends('layouts.sidebar')

@section('main')
    <div>
        <h5>Create Workspace</h5>
        <form method="post">
            @csrf
            <div>
                <label for="title">Title:</label><br>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required><br>
                @error('title')
                    <p>{{ $message }}</p><br>
                @enderror
            </div><br>
            <div>
                <label for="description">Description:</label><br>
                <textarea name="description" id="description" cols="30" rows="10">{{ old('description') }}</textarea><br>
                @error('description')
                    <p>{{ $message }}</p><br>
                @enderror
            </div><br>

            <button type="submit">Create</button>
        </form>
    </div>
@endsection