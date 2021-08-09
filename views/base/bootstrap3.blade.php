<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Forms Example - Twitter Bootstrap 3 - {{ Text::title($theme) }}</title>

    <!-- Bootstrap core CSS -->
    <link href="http://bootswatch.com/3/{{ $theme }}/bootstrap.min.css" rel="stylesheet">
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
            <a class="navbar-brand" href="#">Twitter Bootstrap 3 - {{ Text::title($theme) }}</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                @foreach ($themes as $detail)
                    @if(empty($detail->items))
                        <li><a href="?theme={{ $detail->name }}--{{ $detail->name }}">{{ Text::title( $detail->name) }}</a></li>
                    @else
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ Text::title($detail->name) }}<b
                                    class="caret"></b></a>
                        <ul class="dropdown-menu">
                            @foreach ($detail->items as $option)
                                <li><a href="?theme={{ $detail->name }}-{{ $option }}">{{ Text::title($option) }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    @endif
                @endforeach
                <li class="divider-vertical"></li>
                <li class="dropdown">
                    <a class="dropdown-toggle" href="#" data-toggle="dropdown">
                        Sign In <strong class="caret"></strong></a>

                    <div class="dropdown-menu" style="padding: 15px; padding-bottom: 0px;">
                        <!-- Login form here -->
                        {!! $form('POST', 'examples/_016_forms/users/signin') !!}
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
                {!! $form('POST', 'examples/_016_forms/users/signup') !!}
            </div>
        </div>
    </div>
</div>
<!-- /container -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
</body>
</html>
