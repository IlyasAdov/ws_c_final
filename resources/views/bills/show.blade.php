@extends('layouts.sidebar')

@section('main')
    <div>
        <h5>{{ $monthName }} {{ $year }}</h5>
        <table>
            <thead>
                <th>Token</th>
                <th>Time</th>
                <th>Per sec.</th>
                <th>Total</th>
            </thead>
            <tbody>
                @foreach ($apiTokens as $token)
                    @if ($token->usages->isNotEmpty())
                        <tr>
                            <td>{{ $token->name }} token</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                        @foreach ($token->usages->groupBy('service_id') as $serviceId => $usages)
                            @php
                                $service = $services->find($serviceId);
                                $totalDuration = $usages->sum('duration_in_ms') / 1000;
                                $totalCost = $usages->sum('duration_in_ms') * $service->cost_per_ms;
                            @endphp

                            <tr>
                                <td style="padding-left: 15px">{{ $service->name }}</td>
                                <td>{{ number_format($totalDuration, 3) }}s</td>
                                <td>${{ sprintf('%.4f', $service->cost_per_ms) }}</td>
                                <td>${{ number_format($totalCost, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
                <tr>
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td>${{ number_format($total, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection