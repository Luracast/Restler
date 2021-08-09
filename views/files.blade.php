<html>
<head>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1>Uploading Files</h1>
    <h3>with different http methods</h3>
    <div class="well">
        <h4>Upload by POST</h4>
        {!! $forms->get() !!}
    </div>

    <div class="well">
        <h4>Upload by PUT</h4>
        {!! $forms->get('PUT') !!}
    </div>

    <div class="well">
        <h4>Upload by PATCH</h4>
        {!! $forms->get('PATCH','tests/upload/files') !!}
    </div>
</div>
</body>
