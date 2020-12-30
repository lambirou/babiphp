<!DOCTYPE html>
<html>

<head>
	<title>{{ $page->title }}</title>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="Lambirou">
	<link rel="icon" href="{{ asset('img/favicon.png') }}">

	<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/demo.css') }}">

	<!--  Android 5 Chrome Color -->
	<meta name="theme-color" content="#7300a0">
</head>

<body>
	<div class="container">
		<h1>{{ $page->title }}</h1>
	</div>

	<!-- begin page content -->

	{!! view_content() !!}

	<!-- end page content -->

	<script src="{{ asset('js/demo.js') }}"></script>
</body>

</html>