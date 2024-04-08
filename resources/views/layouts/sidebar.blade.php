@extends('layouts.main')

@section('content')
    <aside>
        <p>API Dashboard</p>
        <nav>
            <ul>
                <li><a href="{{ route('workspaces') }}">Workspaces</a></li>
                <hr>
                <li><a href="{{ route('logout') }}">Logout</a></li>
            </ul>
        </nav>
    </aside>
    <div class="body-wrapper">
        @yield('main')
    </div>
@endsection