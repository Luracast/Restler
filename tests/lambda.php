<?php declare(strict_types=1);


use Lambda\Convert;
use Luracast\Restler\Defaults;
use Luracast\Restler\Restler;
use Luracast\Restler\Utils\Dump;
use Luracast\Restler\Utils\Text;
use Psr\Http\Message\UriInterface;

include __DIR__ . "/../vendor/autoload.php";

var_dump(Text::common('dev/public/index.php', 'dev/path/something'));

exit;

$data = '{"invocationId":"b8a9ec18-6445-4099-b5b7-8908a6ff6fb7","headers":{"content-type":"application\/json","lambda-runtime-aws-request-id":"b8a9ec18-6445-4099-b5b7-8908a6ff6fb7","lambda-runtime-deadline-ms":"1585968628811","lambda-runtime-invoked-function-arn":"arn:aws:lambda:ap-southeast-1:787614641643:function:echo","lambda-runtime-trace-id":"Root=1-5e87f5f1-66789b60ebdeae300cd91ca0;Parent=134e61f8469880c0;Sampled=0","date":"Sat, 04 Apr 2020 02:50:25 GMT","transfer-encoding":"chunked"},"statusCode":200,"requestBody":{"resource":"\/","path":"\/","httpMethod":"GET","headers":{"accept":"text\/html,application\/xhtml+xml,application\/xml;q=0.9,image\/webp,image\/apng,*\/*;q=0.8,application\/signed-exchange;v=b3;q=0.9","accept-encoding":"gzip, deflate, br","accept-language":"en-US,en;q=0.9,ta;q=0.8","cache-control":"max-age=0","dnt":"1","Host":"cz7kagh2l9.execute-api.ap-southeast-1.amazonaws.com","sec-fetch-dest":"document","sec-fetch-mode":"navigate","sec-fetch-site":"none","sec-fetch-user":"?1","upgrade-insecure-requests":"1","User-Agent":"Mozilla\/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/80.0.3987.149 Safari\/537.36","X-Amzn-Trace-Id":"Root=1-5e87f5f1-66789b60ebdeae300cd91ca0","X-Forwarded-For":"101.127.246.210","X-Forwarded-Port":"443","X-Forwarded-Proto":"https"},"multiValueHeaders":{"accept":["text\/html,application\/xhtml+xml,application\/xml;q=0.9,image\/webp,image\/apng,*\/*;q=0.8,application\/signed-exchange;v=b3;q=0.9"],"accept-encoding":["gzip, deflate, br"],"accept-language":["en-US,en;q=0.9,ta;q=0.8"],"cache-control":["max-age=0"],"dnt":["1"],"Host":["cz7kagh2l9.execute-api.ap-southeast-1.amazonaws.com"],"sec-fetch-dest":["document"],"sec-fetch-mode":["navigate"],"sec-fetch-site":["none"],"sec-fetch-user":["?1"],"upgrade-insecure-requests":["1"],"User-Agent":["Mozilla\/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/80.0.3987.149 Safari\/537.36"],"X-Amzn-Trace-Id":["Root=1-5e87f5f1-66789b60ebdeae300cd91ca0"],"X-Forwarded-For":["101.127.246.210"],"X-Forwarded-Port":["443"],"X-Forwarded-Proto":["https"]},"queryStringParameters":null,"multiValueQueryStringParameters":null,"pathParameters":null,"stageVariables":null,"requestContext":{"resourceId":"mblhkunp49","resourcePath":"\/","httpMethod":"GET","extendedRequestId":"KcNduFrSyQ0FYeg=","requestTime":"04\/Apr\/2020:02:50:25 +0000","path":"\/dev\/","accountId":"787614641643","protocol":"HTTP\/1.1","stage":"dev","domainPrefix":"cz7kagh2l9","requestTimeEpoch":1585968625373,"requestId":"4a822dca-51db-4e77-8cc6-d7ef79013c6b","identity":{"cognitoIdentityPoolId":null,"accountId":null,"cognitoIdentityId":null,"caller":null,"sourceIp":"101.127.246.210","principalOrgId":null,"accessKey":null,"cognitoAuthenticationType":null,"cognitoAuthenticationProvider":null,"userArn":null,"userAgent":"Mozilla\/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/80.0.3987.149 Safari\/537.36","user":null},"domainName":"cz7kagh2l9.execute-api.ap-southeast-1.amazonaws.com","apiId":"cz7kagh2l9"},"body":null,"isBase64Encoded":false},"environment":{"PATH":"\/usr\/local\/bin:\/usr\/bin\/:\/bin:\/opt\/bin","LD_LIBRARY_PATH":"\/lib64:\/usr\/lib64:\/var\/runtime:\/var\/runtime\/lib:\/var\/task:\/var\/task\/lib:\/opt\/lib","LANG":"en_US.UTF-8","TZ":":UTC","LAMBDA_TASK_ROOT":"\/var\/task","LAMBDA_RUNTIME_DIR":"\/var\/runtime","AWS_REGION":"ap-southeast-1","AWS_DEFAULT_REGION":"ap-southeast-1","AWS_LAMBDA_LOG_GROUP_NAME":"\/aws\/lambda\/echo","AWS_LAMBDA_LOG_STREAM_NAME":"2020\/04\/04\/[$LATEST]94e33b64b0b94024a0d62c66be0dff56","AWS_LAMBDA_FUNCTION_NAME":"echo","AWS_LAMBDA_FUNCTION_MEMORY_SIZE":"128","AWS_LAMBDA_FUNCTION_VERSION":"$LATEST","_AWS_XRAY_DAEMON_ADDRESS":"169.254.79.2","_AWS_XRAY_DAEMON_PORT":"2000","AWS_XRAY_DAEMON_ADDRESS":"169.254.79.2:2000","AWS_XRAY_CONTEXT_MISSING":"LOG_ERROR","_HANDLER":"hello","AWS_LAMBDA_RUNTIME_API":"127.0.0.1:9001","AWS_ACCESS_KEY_ID":"ASIA3OYL5VXV4JRDUTOV","AWS_SECRET_ACCESS_KEY":"\/XA\/BcGH04aRYd5NzrJp9I\/xX71q\/Pfq+uYzn4WF","AWS_SESSION_TOKEN":"IQoJb3JpZ2luX2VjEGMaDmFwLXNvdXRoZWFzdC0xIkgwRgIhANZ0CYtGmnNku8lkynL4\/OZTya3TTXoN9vv6PrnkDKyaAiEAm0xkjhhHSR+TizK5iHRCVoH5PxI3FEqTxlBOmFqdBMAqwwEIbBABGgw3ODc2MTQ2NDE2NDMiDCOKP4\/05WCmfU9AbiqgARVGV+iFPpRBn+2EWhJ9JWTRNFbc8y1geg2bRQhEonPMRHPoXp9Yz2Zy1pQDhBQ5axqyFO1+YB4UjYVVV6dFv0zI4LHLuJ6F9rSh1u7qHMVWUGjFgcY0qv6hLbAB8ogh0AWZtA7E7uy8PO8zXFQ\/RlSIL00nMaCFq0epfoOXW8YlRVM8Wu6Nme7x3AK2UKnOYAzZSl1xwlQ98FpBuIky\/hQw8euf9AU63wHtqmsZRUiMTen\/tuR6XMojHI6Oc+tNGepvNoxeJYLljeucU7nD5TyPP\/hKTjNxbI7b\/6TgWfy9pVMqt8Y7q94YdsC4rHRoqDQKbXT0qEA59ArGmF9P3EEG\/G4iv2ovldhr5TYdBST+9qfOeiEBGUiqeZe+JtLT4fx2pXpNa2FT\/sVNZSBx8yQNYkjzurbCRn6DKWNdXTWJ9oaD1viic64kin5ZLrg8o9UrTeqDoF1csTxEBT0EhYRSIGlQCgzxFbhci8smW6VmLGvPA37vFlpPgm9Dsh\/9P4VAane3jCca"}}';

$data = json_decode($data, true);

//var_dump($data);

$request = Convert::toPSR7($data['requestBody'], $data['headers']);

echo Dump::request($request);

print_r($request->getServerParams());

function getPath(UriInterface $uri, string $scriptName = ''): string
{
    $slash = '/';
    $path = $uri->getPath();
    $path = trim($path, $slash);
    if (empty($scriptName)) {
        $_baseUrl = $uri->withPath('')->withQuery('');
    } else {
        $path = Text::removeCommon($path, ltrim($scriptName, $slash));
        $url = rtrim(strtok((string)$uri, '.?'), $slash);
        $url = substr($url, 0, -strlen($path));
        $uriClass = get_class($uri);
        $_baseUrl = new $uriClass(rtrim($url, $slash));
    }
    print_r(compact('_baseUrl', 'path', 'scriptName'));
    return $path;
}

getPath($request->getUri(), $data['requestBody']['requestContext']['path'] . trim($request->getUri()->getPath(), '/') . 'index.php');

//$response = new \RingCentral\Psr7\Response(201,['ContentType'=>'application/json'],'{}');

//var_dump(Convert::fromPSR7($response));

