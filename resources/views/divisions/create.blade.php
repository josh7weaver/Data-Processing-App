@extends('layouts.default')

@section('content')

<h1>Create a new Division</h1>

{!! Form::open(['route'=> 'division.index']) !!}
	@include('divisions._createEditForm', [
	    'buttonText'=>'Create',
	    'schoolid'=>0,
	    'enrollmentPercentage' => '100'
    ])
{!! Form::close() !!}

@stop