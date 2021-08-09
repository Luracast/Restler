<?php declare(strict_types=1);

use OAuth2\Convert;

include __DIR__ . "/../vendor/autoload.php";

$contentType = 'multipart/form-data; boundary=------------------------7012238ee627584c';
$body = <<<'BODY'
--------------------------7012238ee627584c
Content-Disposition: form-data; name="grant_type"

authorization_code
--------------------------7012238ee627584c
Content-Disposition: form-data; name="code"

cdd4c959d38e171fe32984cbe75db54179d14a83
--------------------------7012238ee627584c
Content-Disposition: form-data; name="client_id"

demoapp
--------------------------7012238ee627584c
Content-Disposition: form-data; name="client_secret"

demopass
--------------------------7012238ee627584c
Content-Disposition: form-data; name="redirect_uri"

http://localhost:8080/examples/_014_oauth2_client/authorized
--------------------------7012238ee627584c
BODY;

$body = str_replace(["\n"],["\r\n"],$body);
//exit;

print_r(Convert::multipartFormData($body, $contentType));


$contentType = 'multipart/form-data; charset=utf-8; boundary=__X_PAW_BOUNDARY__';
$body ='

--__X_PAW_BOUNDARY__
Content-Disposition: form-data; name="grant_type"

authorization_code
--__X_PAW_BOUNDARY__
Content-Disposition: form-data; name="code"

6a1708c42583e45b9370bba610194c294e6a3331
--__X_PAW_BOUNDARY__
Content-Disposition: form-data; name="client_id"

demoapp
--__X_PAW_BOUNDARY__
Content-Disposition: form-data; name="client_secret"

demopass
--__X_PAW_BOUNDARY__
Content-Disposition: form-data; name="redirect_uri"

http://localhost:8080/examples/_014_oauth2_client/authorized
--__X_PAW_BOUNDARY__--



';
$body = str_replace(["\n"],["\r\n"],$body);
print_r(Convert::multipartFormData($body, $contentType));