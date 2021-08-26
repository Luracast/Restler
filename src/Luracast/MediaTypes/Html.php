<?php


namespace Luracast\Restler\MediaTypes;


use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use Illuminate\View\View;
use JsonSerializable;
use Luracast\Restler\ArrayObject;
use Luracast\Restler\Contracts\ContainerInterface;
use Luracast\Restler\Contracts\ResponseMediaTypeInterface;
use Luracast\Restler\Contracts\SessionInterface;
use Luracast\Restler\Data\Route;
use Luracast\Restler\Defaults;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\ResponseHeaders;
use Luracast\Restler\Restler;
use Luracast\Restler\StaticProperties;
use Luracast\Restler\UI\Forms;
use Luracast\Restler\UI\Nav;
use Luracast\Restler\Utils\Convert;
use Luracast\Restler\Utils\Text;
use Mustache_Engine;
use Mustache_LambdaHelper;
use Mustache_Loader_FilesystemLoader;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class Html extends MediaType implements ResponseMediaTypeInterface
{
    public const MIME = 'text/html';
    public const EXTENSION = 'html';

    public const DEPENDENCIES = [
        'blade' => ['Illuminate\View\View', 'illuminate/view:^8 || ^7'],
        'twig' => ['Twig\Environment', 'twig/twig:^3'],
        'mustache' => ['Mustache_Engine', 'mustache/mustache:^2"'],
    ];

    public static ?string $view = null;
    public static string $errorView = 'debug.php';
    /** @var string Choose manual if you want to compute the html in your api method */
    public static string $template = 'php';
    public static bool $handleSession = true;
    public static bool $convertResponseToArray = false;
    public static bool $useSmartViews = true;
    /**
     * @var null|string defaults to template named folder in Defaults::$cacheDirectory
     */
    public static ?string $cacheDirectory = null;
    /**
     * @var array global key value pair to be supplied to the templates. All
     * keys added here will be available as a variable inside the template
     */
    public static array $data = [];
    /**
     * @var string|null set it to the location of your the view files.
     * Defaults to views folder which is same level as vendor directory.
     */
    public static ?string $viewPath = null;
    /**
     * @var array template and its custom extension key value pair
     */
    public static array $customTemplateExtensions = ['blade' => 'blade.php'];
    /**
     * @var bool used internally for error handling
     */
    protected bool $parseViewMetadata = true;
    /**
     * /**
     */
    private Restler $restler;
    private StaticProperties $html;
    private StaticProperties $defaults;
    private ContainerInterface $container;
    private SessionInterface $session;
    private ServerRequestInterface $request;
    private Route $route;

    public function __construct(
        Restler $restler,
        Route $route,
        SessionInterface $session,
        ContainerInterface $container,
        ServerRequestInterface $request,
        StaticProperties $html,
        StaticProperties $defaults,
        Convert $convert
    ) {
        parent::__construct($convert);
        if (!static::$cacheDirectory) {
            static::$cacheDirectory = Defaults::$cacheDirectory;
        }
        if (!static::$viewPath) {
            static::$viewPath = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'views';
        }
        //============ SESSION MANAGEMENT =============//
        if ($html->handleSession) {
            $key = 'flash';
            if ($session->start() && $session->hasFlash($key)) {
                $html->data['flash'] = $session->flash($key);
                $session->unsetFlash($key);
            }
        }
        $this->restler = $restler;
        $this->route = $route;
        $this->session = $session;
        $this->container = $container;
        $this->html = $html;
        $this->defaults = $defaults;
        $this->request = $request;
    }

    public function encode($data, ResponseHeaders $responseHeaders, bool $humanReadable = false)
    {
        if ('manual' === $this->html['template'] && is_string($data)) {
            return $data;
        }
        try {
            if (!is_readable($this->html->viewPath)) {
                throw new HttpException(
                    501,
                    'The views directory `'
                    . $this->html->viewPath . '` should exist with read permission.'
                );
            }
            $exception = $this->restler->exception;
            $success = is_null($exception);
            $error = $success ? null : $exception->getMessage();
            $data = ArrayObject::fromArray(
                [
                    'response' => $data,
                    'success' => $success,
                    'error' => $error,
                    'restler' => $this->restler,
                    'container' => $this->container,
                    'baseUrl' => $this->restler->baseUrl,
                    'currentPath' => $this->restler->path,
                ]
            );
            $rpath = $this->request->getUri()->getPath();
            $data->resourcePathNormalizer = (!empty($data->currentPath) && Text::endsWith($rpath, '/')) ||
            ('index' !== $data->currentPath && Text::endsWith($rpath, 'index.html'))
                ? '../' : './';
            $data->basePath = $data->baseUrl->getPath();
            $data->path = '/' . trim(
                    str_replace('//', '/', $data->basePath . $this->route->resource['path']),
                    '/'
                );
            //$data->path = '/' . ltrim(explode('index', $data->basePath . $data->currentPath)[0],'/');
            $metadata = $data->api = $this->restler->route;
            $view = $success ? 'view' : 'errorView';
            $value = false;
            if ($this->parseViewMetadata && isset($metadata->{$view})) {
                if (is_array($metadata->{$view})) {
                    $this->html['view'] = $metadata->{$view}['description'];
                    $value = $metadata->{$view}['properties']['value'];
                } else {
                    $this->html['view'] = $metadata->{$view};
                }
            } elseif (!$this->html['view']) {
                $file = explode('/', $this->restler->path);
                $file = end($file);
                $this->html['view'] = $this->guessViewName($file);
            }
            $data->merge(ArrayObject::fromArray($this->html['data']));
            if ($value) {
                $data = $data->nested($value);
                if (is_object($data)) {
                    $data = $data instanceof JsonSerializable
                        ? $data->jsonSerialize()
                        : get_object_vars($data);
                }
                if (!is_array($data)) {
                    $data = ['data' => $data];
                }
                $data = ArrayObject::fromArray($data);
            }
            if (false === ($i = strrpos($this->html['view'], '.'))) {
                $template = $this->html['template'];
            } else {
                $this->html['template'] = $template = substr($this->html['view'], $i + 1);
                $this->html['view'] = substr($this->html['view'], 0, $i);
            }
            if (!file_exists($this->html['cacheDirectory'])) {
                if (!mkdir($this->html['cacheDirectory'], 0770, true)) {
                    throw new HttpException(
                        500,
                        'Unable to create cache directory `' . $this->html['cacheDirectory'] . '`'
                    );
                }
            }
            if (method_exists($class = static::class, $template)) {
                if (isset(self::DEPENDENCIES[$template])) {
                    [$className, $package] = self::DEPENDENCIES[$template];
                    if (!class_exists($className, true)) {
                        throw new HttpException(
                            500,
                            static::class . ' has external dependency. Please run `composer require ' .
                            $package . '` from the project root. Read https://getcomposer.org for more info'
                        );
                    }
                }
                return call_user_func("$class::$template", $data, $humanReadable);
            }
            throw new HttpException(500, "Unsupported template system `$template`");
        } catch (Throwable $throwable) {
            $this->parseViewMetadata = false;
            $this->reset();
            throw $throwable;
        }
    }

    public function guessViewName($path)
    {
        if (empty($path)) {
            $path = 'index';
        } elseif (strpos($path, '/')) {
            $path .= '/index';
        }
        $file = $this->html['viewPath'] . '/' . $path . '.' . $this->getViewExtension();
        $this->html->data['guessedView'] = $file;
        return $this->html['useSmartViews'] && is_readable($file)
            ? $path
            : $this->html->errorView;
    }

    public function getViewExtension()
    {
        return $this->html['customTemplateExtensions'][$this->html['template']] ?? $this->html['template'];
    }

    private function reset(): void
    {
        $this->html->view = 'debug';
        $this->html->template = 'php';
    }

    public function php(ArrayObject $data, $debug = true): string
    {
        if ($this->html->view == 'debug') {
            $this->html->viewPath = dirname(__DIR__) . '/views';
        }
        $view = $this->getViewFile(true);
        if (!is_readable($view)) {
            throw new HttpException(
                500,
                "view file `$view` is not readable. " .
                'Check for file presence and file permissions'
            );
        }
        $path = $this->html->viewPath . DIRECTORY_SEPARATOR;
        $template = function ($view) use ($data, $path) {
            if (!isset($data['form'])) {
                $data['form'] = fn() => call_user_func_array(
                    [$this->container->make(Forms::class), 'get'],
                    func_get_args()
                );
            }
            if (!isset($data['nav'])) {
                $data['nav'] = fn() => call_user_func_array(
                    [$this->container->make(Nav::class), 'get'],
                    func_get_args()
                );
            }
            $_ = function () use ($data, $path) {
                extract($data->getArrayCopy());
                $args = func_get_args();
                $task = array_shift($args);
                switch ($task) {
                    case 'read':
                        $file = $path . $args[0];
                        if (is_readable($file)) {
                            return file_get_contents($file);
                        }
                        break;
                    case 'require':
                    case 'include':
                        $file = $path . $args[0];
                        if (is_readable($file)) {
                            if (
                                isset($args[1]) &&
                                ($arrays = $data->nested($args[1]))
                            ) {
                                $str = '';
                                foreach ($arrays as $arr) {
                                    if ($arr instanceof JsonSerializable) {
                                        $arr = $arr->jsonSerialize();
                                    }
                                    if (is_array($arr)) {
                                        extract($arr);
                                    }
                                    $str .= include $file;
                                }
                                return $str;
                            } else {
                                return include $file;
                            }
                        }
                        break;
                    case 'if':
                        if (count($args) < 2) {
                            $args[1] = '';
                        }
                        if (count($args) < 3) {
                            $args[2] = '';
                        }
                        return $args[0] ? $args[1] : $args[2];
                        break;
                    default:
                        if (isset($data[$task]) && is_callable($data[$task])) {
                            return call_user_func_array($data[$task], $args);
                        }
                }
                return '';
            };
            extract($data->getArrayCopy());
            return @include $view;
        };
        $value = $template($view);
        return is_string($value) ? $value : '';
    }

    public function getViewFile($fullPath = false, $includeExtension = true): string
    {
        $v = $fullPath ? $this->html->viewPath . '/' : '';
        $v .= $this->html->view;
        if ($includeExtension) {
            $v .= '.' . $this->getViewExtension();
        }
        return $v;
    }

    /**
     * @param ArrayObject $data
     * @param bool $debug
     * @return false|string
     * @throws Throwable
     */
    public function twig(ArrayObject $data, $debug = true)
    {
        $loader = new FilesystemLoader($this->html->viewPath);
        $twig = new Environment(
            $loader, [
                       'cache' => static::$cacheDirectory ?? false,
                       'debug' => $debug,
                       'use_strict_variables' => $debug,
                   ]
        );
        if ($debug) {
            $twig->addExtension(new DebugExtension());
        }

        $twig->addFunction(
            new TwigFunction(
                'form',
                [$this->container->make(Forms::class), 'get'],
                ['is_safe' => ['html']]
            )
        );
        $twig->addFunction(
            new TwigFunction(
                'form_key',
                [$this->container->make(Forms::class), 'key']
            )
        );
        $twig->addFunction(
            new TwigFunction(
                'nav',
                [$this->container->make(Nav::class), 'get']
            )
        );

        $twig->registerUndefinedFunctionCallback(
            function ($name) {
                if (
                    isset($this->html->data[$name]) &&
                    is_callable($this->html->data[$name])
                ) {
                    return new TwigFunction(
                        $name,
                        $this->html->data[$name]
                    );
                }
                return false;
            }
        );
        $template = $twig->load($this->getViewFile());
        $data = $data->getArrayCopy() ?? [];
        return $template->render($data) ?? '';
    }

    public function handlebar(ArrayObject $data, $debug = true): string
    {
        return $this->mustache($data, $debug);
    }

    public function mustache(ArrayObject $data, $debug = true): string
    {
        $options = [
            'loader' => new Mustache_Loader_FilesystemLoader(
                $this->html->viewPath,
                ['extension' => $this->getViewExtension()]
            ),
            'helpers' => [
                'form' => function ($text, Mustache_LambdaHelper $m) {
                    $params = explode(',', $m->render($text));
                    return call_user_func_array(
                        [$this->container->make(Forms::class), 'get'],
                        $params
                    );
                },
                'nav' => function ($text, Mustache_LambdaHelper $m) {
                    $params = explode(',', $m->render($text));
                    return call_user_func_array(
                        [$this->container->make(Nav::class), 'get'],
                        $params
                    );
                },
                'title' => fn($text, Mustache_LambdaHelper $m) => Text::title($m->render($text)),

            ]
        ];
        if (!$debug) {
            $options['cache'] = $this->html->cacheDirectory;
        }
        $m = new Mustache_Engine($options);
        return $m->render($this->getViewFile(), $data);
    }

    public function blade(ArrayObject $data, $debug = true)
    {
        $resolver = new EngineResolver();
        $filesystem = new Filesystem();
        $compiler = new BladeCompiler($filesystem, $this->html->cacheDirectory);
        $engine = new CompilerEngine($compiler);
        $resolver->register(
            'blade',
            fn() => $engine
        );
        $phpEngine = new PhpEngine($filesystem);
        $resolver->register(
            'php',
            fn() => $phpEngine
        );
        $restler = $this->restler;
        //Lets expose shortcuts for our classes
        spl_autoload_register(
            function ($className) use ($restler): bool {
                if ($found = $this->route->scope[$className] ?? null) {
                    return class_alias($found, $className);
                }
                if (isset(Defaults::$aliases[$className])) {
                    return class_alias(Defaults::$aliases[$className], $className);
                }
                return false;
            },
            true,
            true
        );

        $viewFinder = new FileViewFinder($filesystem, [$this->html->viewPath]);
        $factory = new Factory($resolver, $viewFinder, new Dispatcher());
        $path = $viewFinder->find($this->html->view);
        $data->form = fn() => call_user_func_array(
            [$this->container->make(Forms::class), 'get'],
            func_get_args()
        );
        $data->nav = fn() => call_user_func_array(
            [$this->container->make(Nav::class), 'get'],
            func_get_args()
        );
        $view = new View($factory, $engine, $this->html->view, $path, $data);
        $factory->callCreator($view);
        return $view->render();
    }
}
