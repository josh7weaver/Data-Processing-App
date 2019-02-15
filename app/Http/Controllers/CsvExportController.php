<?php namespace DataStaging\Http\Controllers;

use DataStaging\Http\Requests;
use DataStaging\Models\ViewTbbEnrollment;
use DataStaging\ProcessApplicationLocker;
use Illuminate\Http\Response;

class CsvExportController extends Controller
{
    public function exportTbbEnrollmentCounts(ViewTbbEnrollment $tbbEnrollmentCounts, ProcessApplicationLocker $processLock)
    {
        if($processLock->isLocked())
        {
            return redirect()->back()->withErrors('The datastaging process is currently running. Try again in 10 min or so!');
        }

        return response(
            $tbbEnrollmentCounts->toCsvString(),
            Response::HTTP_OK,
            $tbbEnrollmentCounts->getHeaders()
        );
    }


}
