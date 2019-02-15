<nav style="padding: 20px; margin-bottom:20px; background: lightgrey;overflow:hidden;">
    <div style="float:left">
        @if(isset($currentUser))
            {!! link_to_route('school.index', 'School') !!} |
            {!! link_to_route('division.index', 'Division') !!} |
            {!! link_to_route('reports.latest', 'Reports') !!} |
            {!! link_to_route('csv.tbbEnrollment', 'Download Tbb Enrollment Data') !!}
        @endif
    </div>

    <div style="float:right">
        @if(isset($currentUser))
            You are logged in, {{ $currentUser->email }}.
            {!! link_to_route('logout', 'Logout') !!}
        @else
            {!! link_to_route('login', 'Login') !!}
        @endif
    </div>

</nav>