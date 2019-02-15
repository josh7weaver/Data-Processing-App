<h3>The Following Files are not Valid</h3>
<ul>
    @foreach($fileErrors as $fileError)
        <li><b>{{ $fileError->school->getName() }}</b> / {{$fileError->getFileType()}}: {{$fileError->getMessage()}}</li>
    @endforeach
</ul>

@unless($generalErrors->isEmpty())
<h3>General Errors</h3>
<ul>
    @foreach($generalErrors as $generalError)
        <li><b>{{ $generalError->school->getName() }}</b> / A general error has occurred. The update process did not complete. Please contact softwaresupport@tolbookstores.com for a resolution.</li>
    @endforeach
</ul>
@endunless

<p>
    {!! link_to_route('reports.index', 'See full error report', [$processToken]) !!}
</p>