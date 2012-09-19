<?php
/*
Title: Access Control
Tagline: Who can do what
Tags: access-control, acl, secure, authentication, authorization
Requires: PHP >= 5.3
Description:
This example shows how you can extend the authentication system to create
a robust access control system. As a added bonus we also restrict api
documentation based on the same.

When the `api_key` is

- blank you will see the public api
- `12345` you will see the api that is accessible by an user
- `67890` you will see all api as you have the admin rights

Try it out yourself [here](explorer/index.html#!/v1)
*/
require_once '../../../vendor/restler.php';
use Luracast\Restler\Restler;

$r = new Restler();
$r->addAPIClass('Access', '');
$r->addAPIClass('Resources');
$r->addAuthenticationClass('AccessControl');
$r->handle();