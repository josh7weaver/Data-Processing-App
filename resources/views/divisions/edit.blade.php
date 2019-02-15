@extends('layouts.default')

@section('content')

<h1>Edit Division {{ $division->name }}</h1>

{!! Form::model(
    $division,
    [
        'route' => [
            'division.update',
            $division->id
        ],
        'method' => 'PATCH'
    ]
)!!}

    @include('divisions._createEditForm', [
        'buttonText'=>'Update',
        'schoolid'=> "{$division->school->id}",
        'enrollmentPercentage' => $division->getPrettyEnrollmentPercentage()
    ])

{!! Form::close() !!}

@stop