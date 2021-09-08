<?php
use App\Domain\Convertor\Str;
?>
<style>
    table {
        border-collapse: collapse;
        width: 100%;
        font-size: 8px;
    }

    table, th, td {
        border: 1px solid black;
    }
</style>
<table>
    <tr>
        @foreach(['#',
            __('From Wallet'),
            __('From Currency'),
            __('Sent'),
            __('In USD'),
            __('From Rate'),
            __('To Wallet'),
            __('To Currency'),
            __('Received'),
            __('In USD'),
            __('To Rate'),
            __('Profit'),
            __('Note'),
            __('Date'),] as $heading)
        <th>{{ $heading }}</th>
        @endforeach
    </tr>
    @foreach($exchanges as $e)
    <tr>
        <td>{{ $e->exchange_id }}</td>,
        <td>{{ $e->senderWallet->name }}</td>,
        <td>{{ $e->senderRate->crypto->name }}</td>,
        <td>{{ Str::trimZeroes($e->sender_amount) }}</td>,
        <td>{{ number_format($e->sender_usd_amount, 2) }}</td>,
        <td>{{ Str::trimZeroes($e->senderRate->rate) }}</td>,
        <td>{{ $e->receiverWallet->name }}</td>,
        <td>{{ $e->receiverRate->crypto->name }}</td>,
        <td>{{ Str::trimZeroes($e->receiver_amount) }}</td>,
        <td>{{ number_format($e->receiver_usd_amount, 2) }}</td>,
        <td>{{ Str::trimZeroes($e->receiverRate->rate) }}</td>,
        <td>{{ $e->profit }}</td>,
        <td>{{ $e->note }}</td>
        <td>{{ $e->created_at }}</td>
    </tr>
    @endforeach
</table>
