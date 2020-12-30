<div class="container">

    <h2>{{ $page->name }}
        <small>{{ config('app.description') }}</small>
    </h2>
    <p>
        Your application is now ready. Thank you for choosing this framework for the development of your application.
    </p>
    <p>
        You can start working on it.
    </p>

    <div>
        <a href="{{ url('post') }}">See a post sample page</a>
    </div>
    <div>
        <a href="{{ url('portfolio/John') }}">Go to the portfolio</a>
    </div>

</div>