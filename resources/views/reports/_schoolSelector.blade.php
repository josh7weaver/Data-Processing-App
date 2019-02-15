{!! Form::open(['route'=> 'reports.show']) !!}

    <div class="form-group @if($errors->has('school_id')) has-error @endif">
        {!! Form::label('school', 'School:') !!}
        {!! Form::select('school_id', $schools, $schoolid, ['class'=>'form-control']) !!}

        @if($errors->has('school_id'))
            <span class="text-danger">{{ $errors->first('school_id') }}</span>
        @endif
    </div>

{!! Form::close() !!}