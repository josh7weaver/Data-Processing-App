<?php namespace DataStaging\Http\Controllers;

use DataStaging\Models\Division;
use DataStaging\Models\School;
use DataStaging\Http\Requests\DivisionRequest;
use Auth;

class DivisionsController extends Controller {

	private $division, $school;

	public function __construct(Division $division, School $school)
	{
		$this->division = $division;
		$this->school = $school;
	}

	/**
	 * Display a listing of the resource.
	 * GET /divisions
	 *
	 * @return Response
	 */
	public function index()
    {
        $divisions = $this->division->with('school')->get();

        return view('divisions.index', compact('divisions'));
    }

	/**
	 * Show the form for creating a new resource.
	 * GET /divisions/create
	 *
	 * @return Response
	 */
	public function create()
	{
        $schools = $this->school->buildDropdownOptions('Select a School');

		return view('divisions.create', compact('schools'));
	}

    /**
     * Store a newly created resource in storage.
     * POST /divisions
     *
     * @param DivisionRequest $request
     * @return Response
     */
	public function store(DivisionRequest $request)
	{
		$this->division->fill( $request->allTransformed() )->save();

		return redirect()->route('division.index')
                         ->withMessage("Successfully added new division!");
	}

	/**
	 * Display the specified resource.
	 * GET /divisions/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        $division = $this->division->find($id);

		return view('divisions.show', compact('division'));
	}

    /**
     * Show the form for editing the specified resource.
     * GET /divisions/{id}/edit
     *
     * @param  int $id
     * @return Response
     * @internal param DivisionRequest $request
     */
	public function edit($id)
	{
		$division = $this->division->find($id);
        $schools = $this->school->buildDropdownOptions();

		return view('divisions.edit', compact('division', 'schools') );
	}

    /**
     * Update the specified resource in storage.
     * PUT /divisions/{id}
     *
     * @param  int $id
     * @param DivisionRequest $request
     * @return Response
     */
	public function update($id, DivisionRequest $request)
	{
        $this->division->find($id)->fill($request->allTransformed())->save();

		return redirect()->route('division.index')
                         ->withMessage("Successfully updated division!");
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /divisions/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        $name = $this->division->find($id)->name;
		$this->division->destroy($id);

		return redirect()->route('division.index')
                         ->withMessage("Deleted division '$name' successfully!");
	}

}