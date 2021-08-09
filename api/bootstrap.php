<?php

declare(strict_types=1);

use Auth\Client;
use Auth\Server;
use improved\Authors as ImprovedAuthors;
use Luracast\Restler\Data\ErrorResponse;
use Luracast\Restler\Defaults;
use Luracast\Restler\Filters\RateLimiter;
use Luracast\Restler\MediaTypes\Html;
use Luracast\Restler\MediaTypes\Json;
use Luracast\Restler\MediaTypes\Upload;
use Luracast\Restler\MediaTypes\Xml;
use Luracast\Restler\Middleware\SessionMiddleware;
use Luracast\Restler\OpenApi3\Explorer;
use Luracast\Restler\Restler;
use Luracast\Restler\Routes;
use Luracast\Restler\UI\Forms;
use ratelimited\Authors as RateLimitedAuthors;
use SomeVendor\v1\BMI as VendorBMI1;
use v1\BodyMassIndex as BMI1;

define('BASE', dirname(__DIR__));
include BASE . "/vendor/autoload.php";

Defaults::$cacheDirectory = BASE . '/api/common/store';
Defaults::$implementations[DataProviderInterface::class] = [SerializedFileDataProvider::class];
Defaults::$useUrlBasedVersioning = true;
Defaults::$apiVendor = "SomeVendor";
Defaults::$useVendorMIMEVersioning = true;
Defaults::$implementations[HttpClientInterface::class] = [SimpleHttpClient::class];
Routes::setApiVersion(2);
Html::$template = 'blade'; //'handlebar'; //'twig'; //'php';
Restler::$middleware[] = new SessionMiddleware();

try {
    Defaults::$productionMode = false;
    //
    //---------------------------- MEDIA TYPES -----------------------------
    //
    Routes::setOverridingResponseMediaTypes(
        Json::class,
        Xml::class,
        Html::class
    );
    Routes::setOverridingRequestMediaTypes(
        Json::class,
        Upload::class
    );
    //
    //---------------------------- AUTH CLASSES ----------------------------
    //
    SimpleAuth::setIncludedPaths('examples/_005_protected_api');
    Routes::addAuthenticator(SimpleAuth::class);
    //
    KeyAuth::setIncludedPaths('examples/_009_rate_limiting');
    Routes::addAuthenticator(KeyAuth::class);
    //
    AccessControl::setIncludedPaths('examples/_010_access_control');
    Routes::addAuthenticator(AccessControl::class);
    //
    Server::setIncludedPaths('examples/_015_oauth2_server');
    Routes::addAuthenticator(Server::class);
    //
    //------------------------------ FILTERS ------------------------------
    //
    RateLimiter::setLimit('hour', 10);
    RateLimiter::setIncludedPaths('examples/_009_rate_limiting');
    Forms::setIncludedPaths('examples/_016_forms');
    Routes::setFilters(RateLimiter::class, Forms::class);
    //
    //---------------------------- API CLASSES ----------------------------
    //
    Routes::mapApiClasses(
        [
            //utility api for running behat tests
            'examples/-storage-' => Storage::class,
            //examples
            'examples/_001_helloworld/say' => Say::class,
            'examples/_002_minimal/math' => Math::class,
            'examples/_003_multiformat/bmi' => BMI::class,
            'examples/_004_error_response/currency' => Currency::class,
            'examples/_005_protected_api' => Simple::class,
            'examples/_005_protected_api/secured' => Secured::class,
            'examples/_006_routing/api' => Api::class,
            'examples/_007_crud/authors' => Authors::class,
            'examples/_008_documentation/authors' => ImprovedAuthors::class,
            'examples/_009_rate_limiting/authors' => RateLimitedAuthors::class,
            'examples/_010_access_control' => Access::class,
            'examples/_011_versioning/bmi' => BMI1::class,
            'examples/_012_vendor_mime/bmi' => VendorBMI1::class,
            'examples/_013_html/tasks' => Tasks::class,
            'examples/_014_oauth2_client' => Client::class,
            'examples/_015_oauth2_server' => Server::class,
            'examples/_016_forms/users' => Users::class,
            //tests
            'tests/helper/functions' => Functions::class,
            'tests/param/minmax' => MinMax::class,
            'tests/param/minmaxfix' => MinMaxFix::class,
            'tests/param/type' => Type::class,
            'tests/param/header' => Header::class,
            'tests/param/validation' => Validation::class,
            'tests/request_data' => Data::class,
            'tests/upload/files' => Files::class,
            'tests/storage/cache' => CacheTest::class,
            'tests/storage/session' => SessionTest::class,
            'tests/overrides/method' => Method::class,
            'tests/overrides/property' => Property::class,
            //Explorer
            'explorer' => Explorer::class,
            //GraphQL
            //GraphQL::class,
        ]
    );
    require __DIR__ . '/examples/_018_graphql/routes.php';

} catch (Throwable $t) {
    die(json_encode((new ErrorResponse($t, true))->jsonSerialize(), JSON_PRETTY_PRINT));
}


//$routes = Router::toArray();
//var_export($routes);
//var_export(array_sort(array_keys($routes['v1'])));
//var_export(array_sort(array_keys($routes['v2'])));
//var_export(Router::$formatMap);
