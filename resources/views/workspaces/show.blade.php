@extends('layouts.sidebar')

@section('main')
    <h2>Workspace '{{ $workspace->title }}' <a href="{{ route('workspaces.edit', ['workspaceId' => $workspace->id]) }}">Edit Workspace</a></h2>

    @if (session('action') && session('action') == 'workspaceUpdated')
        <p>Рабочая область изменена.</p>
    @endif

    <div>
        <h4>API Tokens <a href="{{ route('token.create', ['workspaceId' => $workspace->id]) }}">Create new Token</a></h4>
        @if (!$workspace->apiTokens)
            <p>В этой рабочей области нет API токенов</p>
        @else
        @if (session('action') && session('action') == 'tokenRevoked')
            <p>Токен отозван</p>
        @endif
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Статус</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($workspace->apiTokens as $apiToken)
                        <tr>
                            <td>{{ $apiToken->name }}</td>
                            <td>
                                @if ($apiToken->revoked_at)
                                    Отозван {{ $apiToken->revoked_at }}
                                @else
                                    Активен
                                @endif
                            </td>
                            <td>
                                @if (!$apiToken->revoked_at)
                                    <form action="{{ route('token.revoked', ['workspaceId' => $workspace->id, 'tokenId' => $apiToken->id]) }}" method="post">
                                        @csrf
                                        <button type="submit">Отозвать</button>
                                    </form>                                
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div>
        <h4>Платежная квота</h4>
        @if (session('action') && session('action') == 'quotaUpdated')
            <p>Квота изменена.</p>
        @endif
        <table>
            <tr>
                <th>Использовано в текущем месяце</th>
                <td>${{ round($costsCurrentMonth, 2) }}</td>
            </tr>
            @if ($workspace->billingQuota)
                <tr>
                    <th>Оставшаяся сумма доступна</th>
                    <td>${{ round($workspace->billingQuota->limit - $costsCurrentMonth, 2) }}
                        @if ($workspace->billingQuota->limit - $costsCurrentMonth < 0)
                            <span>Превышено</span>                        
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Дней осталось до сброса</th>
                    <td>{{ $daysLeftCurrentMonth }}</td>
                </tr>
            @endif
            <tr>
                <th>Платежная квота</th>
                <td>
                    @if ($workspace->billingQuota)
                        ${{ $workspace->billingQuota->limit }} <br>
                        <a href="{{ route('quota.set', ['workspaceId' => $workspace->id]) }}">Изменить платежную квоту</a>
                    @else
                        Не установлена платежная квота <br>
                        <a href="{{ route('quota.set', ['workspaceId' => $workspace->id]) }}">Установить платежную квоту</a>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div>
        <h4>Счета</h4>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bills as $bill)
                    <tr>
                        <td>{{ $bill->format('F Y') }}</td>
                        <td><a href="{{ route('bills', ['workspaceId' => $workspace->id, 'year' => $bill->format('Y'), 'month' => $bill->format('m')]) }}">Show</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection