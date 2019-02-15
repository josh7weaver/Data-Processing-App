<h2>{{ $schoolName }}</h2>

<p>
    {!! link_to_route('reports.show', 'See full error report', [$processToken, $schoolCode]) !!}
</p>

<div id="customer">
    @foreach($rowErrors as $fileName => $errorGroup)

        <h4>{{$fileName}} File:</h4>
        @forelse($errorGroup as $errorItem)
            {{ $errorItem->getValidationCount() }} row(s) invalid: {{ $errorItem->getSummary() }}<br />
        @empty
            <p>No {{ $fileName }} File Errors.</p>
        @endforelse

    @endforeach
</div>