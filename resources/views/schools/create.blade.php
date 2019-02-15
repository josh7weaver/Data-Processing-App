@extends('layouts.default')

@section('content')

<h1>Create School</h1>

{!! Form::open(['route' => 'school.store'])!!}
    @include('schools/_createEditForm', ['buttonText'=>'Create School'])
{!! Form::close() !!}

@stop