@extends('layouts.default')

@section('content')
            <h1>SCHOOLS</h1>

            <div class="pad-md">
                <a href="{{ route('school.create') }}" class="btn btn-success">Add new school</a>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>School Name</th>
                        <th>Is it Enabled?</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>


                    @foreach ($schools as $school)

                        <tr @unless($school->enabled)class="enabled-row" @endunless>
                            <td>{{ $school->id }}</td>
                            <td>{{ $school->name }}</td>
                            <td>
                                @if ($school->enabled)<span class="bold">yes</span> @else no @endif
                            </td>
                            <td>
                                <a href="{{ route('school.edit', [$school->id]) }}">Edit</a>
                                {{--|--}}
{{--                                <a href="{{ route('school.destroy', [$school->id]) }}" data-method="delete"  data-token="{{ csrf_token() }}">Delete</a>--}}

                            </td>
                        </tr>

                    @endforeach

                </tbody>
            </table>

            <div class="pad-bottom-md">
                <a href="{{ route('school.create') }}" class="btn btn-success">Add new school</a>
            </div>

@stop