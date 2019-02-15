@extends('layouts.default')

@section('content')
    {{--
        THIS DROPDOWN SUBMITS via JAVASCRIPT
    --}}
    {!! Form::select('school_id', $schoolDropdown, $schoolId, [
            'class'=>'form-control',
            'id'=>'school-lookup',
            'data-process-token'=> $processToken
    ]) !!}

    {{--Need extra outer foreach to allow for possibility of multiple schools--}}
    @forelse($schoolsErrorList as $schoolErrorDto)

            <h1>{{ $schoolErrorDto->getSchoolName() }}</h1>
            @foreach($schoolErrorDto->getValidationErrors() as $fileType => $presenters)
                <div class="school-validation-errors">

                    <h4>{{ $fileType }} File</h4>

                    <div class="{{$fileType}}-errors">
                        @forelse($presenters as $presenter)

                            <div class="validation-group">
                                <p>
                                    <b>{{ $presenter->getSummary() }}</b>
                                </p>
                                @foreach($presenter->getDetails() as $detail)
                                    {{ $detail }}<br />
                                @endforeach
                            </div>

                        @empty
                        No Errors for {{$fileType}} file.
                        @endforelse
                    </div>

                </div>
            @endforeach
            
            @unless($schoolErrorDto->getGeneralErrors()->isEmpty())
                <h4>General Errors Present</h4>
            @endunless

            <div class="school-general-errors">
                @foreach($schoolErrorDto->getGeneralErrors() as $generalError)
                    <p>
                        {{ $generalError->getMessage() }}
                        <ul>
                            @foreach($generalError->getContext() as $key => $contextItem)
                                <li>
                                    {{ $key }} = {{ $contextItem }}
                                </li>
                            @endforeach
                        </ul>
                    </p>
                @endforeach
            </div>

    @empty
        <br />
        <p>No errors!</p>
    @endforelse
@stop