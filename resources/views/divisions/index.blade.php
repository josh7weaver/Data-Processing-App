@extends('layouts.default')

@section('content')
            <h1>DIVISIONS</h1>

            <div class="pad-md">
                {!! link_to_route('division.create', 'Add new division', [], ['class'=>"btn btn-success"]) !!}
            </div>

			<table class="table">
				<thead>
					<tr>
						<th>ID</th>
						<th>Division Name</th>
						<th>School</th>
						<th>Enrollment Percentage</th>
						{{--<th>Uses Textbook Butler?</th>--}}
						{{--<th>Default Preference</th>--}}
                        <th>Adjust Enrollment?</th>
                        <th>Uses Butler?</th>
                        <th>Is Enabled?</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>


					@foreach ($divisions as $division)

						<tr @unless($division->enabled)class="enabled-row"@endunless>
							<td>{{ $division->id }}</td>
							<td>{{ $division->name }}</td>
							<td>{{ $division->school->name }}</td>
							<td>{{ $division->getPrettyEnrollmentPercentage() }}%</td>
                            <td>
                                @if ($division->enrollment_adjustment_enabled)<span class="bold">yes</span> @else no @endif
                            </td>
                            <td>
                                @if ($division->use_butler)<span class="bold">yes</span> @else no @endif
                            </td>
                            <td>
								@if ($division->enabled)<span class="bold">yes</span> @else no @endif
							</td>
							<td>
								{{--
								<a href="{{ route('division.show', [$division->id]) }}">View</a> |
								--}}
								<a href="{{ route('division.edit', [$division->id]) }}">Edit</a>
                                {{--|--}}
{{--								<a href="{{ route('division.destroy', [$division->id]) }}" data-method="delete" data-token="{{ csrf_token() }}">Delete</a>--}}
							</td>
						</tr>

					@endforeach

				</tbody>
			</table>

			<br /><br />

            <div class="pad-bottom-md">
			    {!! link_to_route('division.create', 'Add new division', [], ['class'=>"btn btn-success"]) !!}
            </div>

@stop