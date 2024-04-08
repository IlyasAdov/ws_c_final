@extends('layouts.sidebar')

@section('main')
    <div>
        <h5>Create API Token</h5>
        <form method="post">
            @csrf
            <div>
                <label for="name">Name:</label><br>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required><br>
                @error('name')
                    <p>{{ message }}</p>
                @enderror
            </div><br>
            <button type="submit">Create</button>
        </form>
    </div>
@endsection