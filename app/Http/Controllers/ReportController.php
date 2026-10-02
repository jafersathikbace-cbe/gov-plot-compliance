<?php

namespace App\Http\Controllers;

use App\Models\AllotmentCase;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function monthly()
    {
        $cases = AllotmentCase::orderBy('created_at', 'desc')->get();
        return Pdf::loadView('pdf.monthly-report', compact('cases'))->download('monthly-compliance-report.pdf');
    }

    public function nonCompliance()
    {
        $cases = AllotmentCase::whereIn('status', ['non_compliant', 'terminated', 'at_risk'])->orderBy('created_at', 'desc')->get();
        return Pdf::loadView('pdf.non-compliance-report', compact('cases'))->download('non-compliance-report.pdf');
    }

    public function certificate(string $caseId)
    {
        $case = AllotmentCase::findOrFail($caseId);
        return Pdf::loadView('pdf.final-certificate', compact('case'))->download('compliance-certificate-'.$caseId.'.pdf');
    }
}
