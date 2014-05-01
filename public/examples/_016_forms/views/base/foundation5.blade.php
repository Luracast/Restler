<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Forms Example - Zerb Foundation 5 </title>

    <!--<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet">-->
    <link href="{{ $baseUrl }}/css/glyphicons.css" rel="stylesheet">
    <link href="//cdnjs.cloudflare.com/ajax/libs/foundation/5.2.2/css/foundation.min.css" rel="stylesheet">
    <!-- <link href="//cdnjs.cloudflare.com/ajax/libs/foundicons/3.0.0/foundation-icons.min.css" rel="stylesheet">-->
    <!--<link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">-->
    <script src="//cdnjs.cloudflare.com/ajax/libs/foundation/5.2.2/js/vendor/modernizr.js"></script>
</head>

<body>
<div class="contain-to-grid sticky">
    <nav class="top-bar" data-topbar>
        <ul class="title-area">
            <li class="name">
                <h1><a href="#">Zerb Foundation 5</a></h1>
            </li>
            <li class="toggle-topbar menu-icon"><a href="#">Menu</a></li>
        </ul>
        <section class="top-bar-section">
            <ul class="alignment right">
                <li class="has-dropdown">
                    <a href="#">Choose Your Style</a>
                    <ul class="dropdown">
                        <li><a href="?theme=foundation5">Foundation</a></li>
                        <li><label>Bootstrap Themes</label></li>
                        @foreach ($themes as $option)
                        <li><a href="?theme={{ $option }}">{{ String::title($option) }}</a></li>
                        @endforeach
                    </ul>
                </li>
                <li class="divider"></li>
                <li class="has-dropdown">
                    <a href="#">Sign In</a>
                    <ul class="dropdown">
                        <li class="has-form">
                            <div class="row" style="min-width: 140px">
                                {{ Forms::get('POST', 'users/signin') }}
                            </div>
                            <p></p>
                        </li>
                    </ul>
                </li>
            </ul>
        </section>
    </nav>
</div>
<p></p>

<div class="container">
    <div class="row">
        <div class="large-4 columns">
            <h1>Forms</h1>
            <p>This example shows how to use the Forms class</p>
        </div>
        <div class="large-8 columns">
            <h3>Sign Up</h3>
            {{ Forms::get('POST', 'users/signup') }}
        </div>
    </div>

</div>
<!-- /container -->
<script src="//cdnjs.cloudflare.com/ajax/libs/foundation/5.2.2/js/vendor/jquery.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/foundation/5.2.2/js/foundation.min.js"></script>
<script>
    $(document).foundation();
</script>
</body>
</html>