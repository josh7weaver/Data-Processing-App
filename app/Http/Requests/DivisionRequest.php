<?php namespace DataStaging\Http\Requests;

use DataStaging\Http\Requests\Request;

class DivisionRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
            'name'                  => 'required',
            'school_id'           => 'required|not_in:0',
            'enrollment_percentage' => ''
        ];
	}

    public function messages()
    {
        return [
            'school_id.not_in' => 'Please select a school',
        ];
    }

    public function allTransformed()
    {
        $enrollmentPercentage = $this->get('enrollment_percentage');

        $percentageDouble = ['enrollment_percentage' => $this->percentageToDouble($enrollmentPercentage)];

        return array_merge(parent::all(), $percentageDouble);
    }

    protected function percentageToDouble($percentage)
    {
        return $percentage / 100;
    }


}
