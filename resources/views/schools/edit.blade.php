@extends('layouts.default')

@section('content')

<h1>Edit {{$school->name}}</h1>

{!! Form::model(
    $school,
    [
        'route' => [
            'school.update',
            $school->id
        ],
        'method' => 'PATCH'
    ]
)!!}

    @include('schools/_createEditForm', ['buttonText'=>'Update School'])

{!! Form::close() !!}

@stop