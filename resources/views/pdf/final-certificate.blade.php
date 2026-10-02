@extends('pdf.layout')
@section('content')
<h1>Final Compliance Certificate</h1>
<p>This certifies that the following allotment case has successfully met all required commitments and is marked fully compliant.</p>
<table>
    <tr><th>Case Title</th><td>{{ $case->title }}</td></tr>
    <tr><th>Status</th><td>{{ $case->status }}</td></tr>
    <tr><th>Investment Required</th><td>{{ $case->investment_required }}</td></tr>
    <tr><th>Employment Required</th><td>{{ $case->employment_required }}</td></tr>
    <tr><th>Closed At</th><td>{{ $case->closed_at }}</td></tr>
</table>
@endsection
