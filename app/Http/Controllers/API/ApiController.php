<?php namespace DataStaging\Http\Controllers\API;

use DataStaging\ProcessApplicationLocker;

class ApiController extends Controller
{

    /**
     * Get the lock status
     * @Get("api/v1/status")
     * @param ProcessApplicationLocker $applicationLocker  -- this is handled by Dep. injection container in laravel
     * @return json
     */
    public function status(ProcessApplicationLocker $applicationLocker)
    {
        if($applicationLocker->isLocked()){
            return \Response::json(['isLocked' => true]);
        }

        return \Response::json(['isLocked' => false]);
    }
}