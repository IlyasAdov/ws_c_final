@extends('layouts.main')

@section('content')
    <div>
        <h1>Api Dashboard</h1>
        <form method="post">
            @csrf
            <div>
                <label for="username">Username:</label><br>
                <input type="text" name="username" id="username" value="{{ old('username') }}" required>
            </div>
            <br>
            <div>
                <label for="password">Password:</label><br>
                <input type="password" name="password" id="password" required>
            </div><br>
            @if ($errors->first('loginFailed'))
                <p>Username and/or password invalid</p>
            @endif
            <button type="submit">Sign in</button>
        </form>
    </div>
@endsection