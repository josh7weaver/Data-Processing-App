<div class="form-group @if($errors->has('name')) has-error @endif">
	{!! Form::label('name', 'Name:') !!}
	{!! Form::text('name', null, ['class'=>'form-control', 'placeholder'=>'Division Name']) !!}

    @if($errors->has('name'))
        <span class="text-danger">{{ $errors->first('name') }}</span>
    @endif
</div>
<div class="form-group @if($errors->has('school_id')) has-error @endif">
    {!! Form::label('school', 'School:') !!}
	{!! Form::select('school_id', $schools, $schoolid, ['class'=>'form-control']) !!}

    @if($errors->has('school_id'))
        <span class="text-danger">{{ $errors->first('school_id') }}</span>
    @endif
</div>

<div class="form-group">
	<fieldset>
		<legend>Adjust Enrollment?</legend>

		{!! Form::label('adjust-enrollment-yes', 'Yes') !!}
		{!! Form::radio('enrollment_adjustment_enabled', 1, false, ['id'=>'adjust-enrollment-yes']) !!}

		{!! Form::label('adjust-enrollment-no', 'No') !!}
		{!! Form::radio('enrollment_adjustment_enabled', 0, true, ['id'=>'adjust-enrollment-no']) !!}

		{!! $errors->first('enrollment_adjustment_enabled') !!}

        <div>
            {!! Form::label('enrollment_percentage', 'Percentage of Enrollment:') !!}
            {!! Form::text('enrollment_percentage', $enrollmentPercentage, ['size'=>5]) !!}%

            {!! $errors->first('enrollment_percentage') !!}
        </div>
	</fieldset>
</div>

<div class="form-group">
    <fieldset>
        <legend>Uses Butler?</legend>

        {!! Form::label('uses-butler-yes', 'Yes') !!}
        {!! Form::radio('use_butler', 1, true, ['id'=>'uses-butler-yes']) !!}

        {!! Form::label('uses-butler-no', 'No') !!}
        {!! Form::radio('use_butler', 0, false, ['id'=>'uses-butler-no']) !!}

        {!! $errors->first('use_butler') !!}
    </fieldset>
</div>

<div class="form-group">
	<fieldset>
		<legend>Enabled?</legend>

		{!! Form::label('enabled-yes', 'Yes') !!}
		{!! Form::radio('enabled', 1, true, ['id'=>'enabled-yes']) !!}

		{!! Form::label('enabled-no', 'No') !!}
		{!! Form::radio('enabled', 0, false, ['id'=>'enabled-no']) !!}

		{!! $errors->first('enabled') !!}
	</fieldset>
</div>

<div>
	{!! Form::submit( isset($buttonText) ? $buttonText : "Submit", ['class'=>'btn btn-success']) !!}
</div>
