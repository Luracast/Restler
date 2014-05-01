<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Forms Example - Twitter Bootsrap 3 - {{ String::title($theme) }}</title>

    <!-- Bootstrap core CSS -->
    <link href="http://bootswatch.com/{{ $theme }}/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<!-- Fixed navbar -->
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Twitter Bootsrap 3 - {{ String::title($theme) }}</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Choose Your Style<b
                            class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="?theme=foundation5">Foundation</a></li>
                        <li class="dropdown-header">Bootstrap Themes</li>
                        @foreach ($themes as $option)
                        <li><a href="?theme={{ $option }}">{{ String::title($option) }}</a></li>
                        @endforeach
                    </ul>
                </li>
                <li class="divider-vertical"></li>
                <li class="dropdown">
                    <a class="dropdown-toggle" href="#" data-toggle="dropdown">
                        Sign In <strong class="caret"></strong></a>

                    <div class="dropdown-menu" style="padding: 15px; padding-bottom: 0px;">
                        <!-- Login form here -->
                        {{ Forms::get('POST', 'users/signin') }}
                        <p>&nbsp;</p>
                    </div>
                </li>
            </ul>
        </div>
        <!--/.nav-collapse -->
    </div>
</div>

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
                {{ Forms::get('POST', 'users/signup') }}
            </div>
        </div>
    </div>
</div>
<!-- /container -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
</body>
</html>
