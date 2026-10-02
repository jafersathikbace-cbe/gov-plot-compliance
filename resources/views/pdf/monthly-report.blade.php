@extends('pdf.layout')
@section('content')
<h1>Monthly Compliance Summary</h1>
<p>Total Cases: {{ $cases->count() }}</p>
<p>Total Submissions: {{ $submissions->count() }}</p>
<table>
    <tr><th>Case</th><th>Status</th><th>Investment Required</th><th>Employment Required</th></tr>
    @foreach($cases as $case)
        <tr>
            <td>{{ $case->title }}</td>
            <td>{{ $case->status }}</td>
            <td>{{ $case->investment_required }}</td>
            <td>{{ $case->employment_required }}</td>
        </tr>
    @endforeach
</table>
@endsection
