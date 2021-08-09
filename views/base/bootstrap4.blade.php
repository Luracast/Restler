<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Forms Example - Twitter Bootstrap 4 - {{ Text::title($theme) }}</title>

    <!-- Bootstrap core CSS -->
    <link href="http://bootswatch.com/4/{{ $theme }}/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-primary">
    <div class="container">
        <a href="#" class="navbar-brand">Twitter Bootstrap 4 - {{ Text::title($theme) }}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive"
                aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav">
                @foreach ($themes as $detail)
                    @if(empty($detail->items))
                        <li class="nav-item">
                            <a class="nav-link"
                               href="?theme={{ $detail->name }}-{{ $detail->name }}">{{ Text::title($detail->name) }}</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#"
                               id="themes">{{ Text::title($detail->name) }}<span class="caret"></span></a>
                            <div class="dropdown-menu" aria-labelledby="themes">
                                @foreach ($detail->items as $option)
                                    <a class="dropdown-item"
                                       href="?theme={{ $detail->name }}-{{ $option }}">{{ Text::title($option) }}</a>
                                @endforeach
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>
            <ul class="nav navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" id="signin">Sign In <span
                                class="caret"></span></a>
                    <div class="dropdown-menu" aria-labelledby="signin">
                        {!! $form('POST', 'examples/_016_forms/users/signin') !!}
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">

    <!-- Main component for a primary marketing message or call to action -->
    <div class="jumbotron">
        <div class="row">
            <div class="col-md-4">
                <h1>Forms</h1>
                <p>This example shows how to use the Forms class</p>
            </div>
            <div class="col-md-8">
                <h3>Sign Up</h3>
                {!! $form('POST', 'examples/_016_forms/users/signup') !!}
            </div>
        </div>
    </div>
</div>
<!-- /container -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx"
        crossorigin="anonymous"></script>
</body>
</html>
