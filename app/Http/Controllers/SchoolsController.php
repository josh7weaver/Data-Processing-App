<?php namespace DataStaging\Http\Controllers;

use DataStaging\Models\School;
use DataStaging\Models\Division;
use DataStaging\Http\Requests\SchoolRequest;

class SchoolsController extends Controller {

	public $school;

	public function __construct(School $school)
	{
		$this->school = $school;
	}

	/**
	 * Display a listing of the resource.
	 * GET /schools
	 *
	 * @return Response
	 */
	public function index()
	{
		$schools = $this->school->with('divisions')->get();

		return view('schools.index', compact('schools'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /schools/create
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('schools.create');
	}

    /**
     * Store a newly created resource in storage.
     * POST /schools
     *
     * @param SchoolRequest $request
     * @return Response
     */
	public function store(SchoolRequest $request)
	{
		$this->school->fill($request->all())->save();

		return redirect()->route('school.index')
                         ->withMessage('New School Added.');
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /schools/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        $school = $this->school->find($id);

		return view('schools.edit', compact('school'));
	}

    /**
     * Update the specified resource in storage.
     * PUT /schools/{id}
     *
     * @param  int $id
     * @param SchoolRequest $request
     * @return Response
     */
	public function update($id, SchoolRequest $request)
	{
		$school = $this->school->find($id);
        $school->fill($request->all())->save();

		return redirect()->route('school.index')
                         ->withMessage('School Updated Successfully!');
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /schools/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$this->school->destroy($id);

		return redirect()->route('school.index')
                         ->withMessage('Deleted School');
	}

}