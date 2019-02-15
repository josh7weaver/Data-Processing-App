<div class="form-group @if($errors->has('name')) has-error @endif">
    {!! Form::label('name', 'School Name:') !!}
    {!! Form::text('name', null, ['class'=>'form-control', 'placeholder'=>'School Name']) !!}

    @if($errors->has('name'))
        <span class="text-danger">{{ $errors->first('name') }}</span>
    @endif
</div>
<fieldset class="form-group">
    <legend>Is School Enabled?</legend>

    <div>
        {!! Form::radio('enabled', '1', true, ['id'=>'enabled_true']) !!}
        {!! Form::label('enabled_true', 'Enabled') !!}
    </div>
    <div>
        {!! Form::radio('enabled', '0', false, ['id'=>'enabled_false']) !!}
        {!! Form::label('enabled_false', 'Disabled') !!}
    </div>
</fieldset>
<div>
    {!! Form::submit($buttonText, ['class'=>'btn btn-success']) !!}
</div>