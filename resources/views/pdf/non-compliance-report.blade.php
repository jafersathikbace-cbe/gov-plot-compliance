@extends('pdf.layout')
@section('content')
<h1>Non-Compliance Report</h1>
<table>
    <tr><th>Case</th><th>Status</th><th>Legal Status</th><th>Construction Deadline</th></tr>
    @foreach($cases as $case)
        <tr>
            <td>{{ $case->title }}</td>
            <td>{{ $case->status }}</td>
            <td>{{ $case->legal_status }}</td>
            <td>{{ $case->construction_deadline }}</td>
        </tr>
    @endforeach
</table>
@endsection
