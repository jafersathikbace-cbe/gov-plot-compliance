@extends('pdf.layout')
@section('content')
<h1>Approval Memo</h1>
<p>This memo confirms approval of the incentive/refund claim.</p>
<table>
    <tr><th>Case</th><td>{{ $case->title }}</td></tr>
    <tr><th>Claim Type</th><td>{{ $claim->type }}</td></tr>
    <tr><th>Amount</th><td>{{ $claim->amount }}</td></tr>
    <tr><th>Status</th><td>{{ $claim->status }}</td></tr>
    <tr><th>Payment Reference</th><td>{{ $claim->payment_reference }}</td></tr>
    <tr><th>Approved At</th><td>{{ $claim->approved_at }}</td></tr>
</table>
@endsection
