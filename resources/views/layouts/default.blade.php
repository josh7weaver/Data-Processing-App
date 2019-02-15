<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href={{asset("assets/css/app.css")}}>
</head>
<body>

	@include('partials.nav')

	<div class="container">

		@if (Session::has('message'))
			<div class="alert alert-success" role="alert">
				{{ Session::get('message') }}
			</div>
        @elseif($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
		@endif

		@yield('content')
	</div>


	<!-- JQUERY IS ONLY INCLUDED FOR the delete http method on links -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <script src={{asset("assets/js/app.js")}}></script>
</body>
</html>