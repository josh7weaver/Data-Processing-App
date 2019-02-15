<?php namespace DataStaging\Http\Controllers;

use DataStaging\Http\Requests;
use DataStaging\Http\Controllers\Controller;

use Illuminate\Http\Request;

class VisitorsController extends Controller {

    public function home(){
        return view('default');
	}

}
