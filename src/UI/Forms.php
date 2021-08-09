<?php

namespace Luracast\Restler\UI;

use Luracast\Restler\Contracts\FilterInterface;
use Luracast\Restler\Contracts\SelectivePathsInterface;
use Luracast\Restler\Contracts\SelectivePathsTrait;
use Luracast\Restler\Contracts\SessionInterface;
use Luracast\Restler\Contracts\UserIdentificationInterface;
use Luracast\Restler\Data\Param;
use Luracast\Restler\Data\Route;
use Luracast\Restler\Data\Type;
use Luracast\Restler\Defaults;
use Luracast\Restler\Exceptions\HttpException;
use Luracast\Restler\MediaTypes\Upload;
use Luracast\Restler\MediaTypes\UrlEncoded;
use Luracast\Restler\ResponseHeaders;
use Luracast\Restler\Restler;
use Luracast\Restler\Routes;
use Luracast\Restler\StaticProperties;
use Luracast\Restler\UI\Tags as T;
use Luracast\Restler\Utils\Text;
use Luracast\Restler\Utils\Validator;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;


/**
 * Utility class for automatically generating forms for the given http method
 * and api url
 *
 * @category   Framework
 * @package    Restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 */
class Forms implements FilterInterface, SelectivePathsInterface
{
    use SelectivePathsTrait;

    public const FORM_KEY = 'form_key';

    public static bool $filterFormRequestsOnly = false;
    public static $style;
    /**
     * @var bool should we fill up the form using given data?
     */
    public static bool $preFill = true;
    protected array $inputTypes = [
        'hidden',
        'password',
        'button',
        'image',
        'file',
        'reset',
        'submit',
        'search',
        'checkbox',
        'radio',
        'email',
        'text',
        'color',
        'date',
        'datetime',
        'datetime-local',
        'email',
        'month',
        'number',
        'range',
        'search',
        'tel',
        'time',
        'url',
        'week',
    ];
    protected bool $fileUpload = false;
    protected UserIdentificationInterface $userIdentifier;

    private array $key = [];
    private ?Route $route = null;
    private Route $currentRoute;
    private Restler $restler;
    private StaticProperties $forms;
    private SessionInterface $session;

    public function __construct(
        Restler $restler,
        Route $route,
        StaticProperties $forms,
        SessionInterface $session
    ) {
        $this->restler = $restler;
        $this->currentRoute = $route;
        $this->forms = $forms;
        $this->session = $session;
    }

    /**
     * Get the form
     *
     * @param string $method http method to submit the form
     * @param string|null $action relative path from the web root. When set to null
     *                         it uses the current api method's path
     * @param bool $dataOnly if you want to render the form yourself use this
     *                         option
     * @param string $prefix used for adjusting the spacing in front of
     *                         form elements
     * @param string $indent used for adjusting indentation
     *
     * @return array|T
     *
     * @throws HttpException
     */
    public function get(
        string $method = 'POST',
        string $action = null,
        bool $dataOnly = false,
        string $prefix = '',
        string $indent = '    '
    ) {
        if (!$this->forms->style) {
            $this->forms->style = FormStyles::$html;
        }

        try {
            if (is_null($action)) {
                $action = $this->currentRoute->path;
            }
            $base = trim($this->restler->baseUrl->getPath(), '/');
            if (Text::beginsWith($action, $base)) {
                $action = substr($action, strlen($base));
            }
            $action = trim($action, '/');
            $current = $this->currentRoute;
            if ((($method == $current->httpMethod) && ($action == $current->path))) {
                /** @var Route route */
                $this->route = $route = $this->currentRoute;
            } else {
                $this->route = $route = Routes::find(
                    trim($action, '/'),
                    $method,
                    null,
                    $this->restler->requestedApiVersion,
                    []
                );
            }
        } catch (HttpException $e) {
            //echo $e->getErrorMessage();
            $route = false;
        }
        if (!$route) {
            throw new HttpException(500, 'invalid action path for form `' . $method . ' ' . $action . '`');
        }
        $r = static::fields($route, $dataOnly);
        if ($method != 'GET' && $method != 'POST') {
            if (empty(Defaults::$httpMethodOverrideProperty)) {
                throw new HttpException(
                    500,
                    'Forms require `Defaults::\$httpMethodOverrideProperty`' .
                    "for supporting HTTP $method"
                );
            }

            if ($dataOnly) {
                $r[] = [
                    'tag' => 'input',
                    'name' => Defaults::$httpMethodOverrideProperty,
                    'type' => 'hidden',
                    'value' => 'method',
                ];
            } else {
                $r[] = T::input()
                    ->name(Defaults::$httpMethodOverrideProperty)
                    ->value($method)
                    ->type('hidden');
            }

            $method = 'POST';
        }
        if ($this->session->getId() != '') {
            $form_key = $this->key($method, $action);
            if ($dataOnly) {
                $r[] = [
                    'tag' => 'input',
                    'name' => static::FORM_KEY,
                    'type' => 'hidden',
                    'value' => 'hidden',
                ];
            } else {
                $key = T::input()
                    ->name(static::FORM_KEY)
                    ->type('hidden')
                    ->value($form_key);
                $r[] = $key;
            }
        }

        $s = [
            'tag' => 'button',
            'type' => 'submit',
            'label' => $route->return->label ?? 'Submit'
        ];

        if (!$dataOnly) {
            $s = Emmet::make($this->style('submit', $route->return), $s);
        }
        $r[] = $s;
        $t = [
            'action' => $this->restler->baseUrl . '/' . trim($action, '/'),
            'method' => $method,
        ];
        if ($this->fileUpload) {
            $this->fileUpload = false;
            $t['enctype'] = 'multipart/form-data';
        }
        //TODO: bring the below functionality
        /*
        if (isset($m[CommentParser::$embeddedDataName])) {
            $t += $m[CommentParser::$embeddedDataName];
        }
        */
        if (!$dataOnly) {
            $t = Emmet::make($this->style('form', $route->return), $t);
            $t->prefix = $prefix;
            $t->indent = $indent;
            $t[] = $r;
        } else {
            $t['fields'] = $r;
        }
        return $t;
    }

    public function fields(Route $route, bool $dataOnly = false)
    {
        $r = [];
        $values = $route->getArguments();
        foreach ($route->parameters as $parameter) {
            if (!$this->fieldable($parameter)) {
                continue;
            }
            $value = $values[$parameter->index] ?? null;
            if (!$this->fillable($parameter, $value)) {
                $value = null;
            }
            if (!empty($parameter->properties)) {
                $t = Emmet::make($this->style('fieldset', $parameter), ['label' => $parameter->label]);
                /**
                 * @var string|int $key
                 * @var  Param $property
                 */
                foreach ($parameter->properties as $key => $property) {
                    if (!$this->fieldable($property)) {
                        continue;
                    }
                    $childValue = $value[$key] ?? null;
                    if (!$this->fillable($property, $childValue)) {
                        $childValue = null;
                    }
                    $property = clone $property;
                    $property->name = sprintf("%s[%s]", $parameter->name, $property->name);
                    $t[] = $this->field($property, $childValue, false);
                }
                $r[] = $t;
            } else {
                $f = $this->field($parameter, $value, false);
                $r[] = $f;
            }
        }
        return $r;
    }

    private function fieldable(Param $parameter): bool
    {
        return Param::FROM_PATH !== $parameter->from && Param::FROM_HEADER !== $parameter->from;
    }

    private function fillable(Param $param, $value): bool
    {
        return $this->forms->preFill && is_scalar($value) ||
            ('array' == $param->type && is_array($value)) ||
            (is_object($value) && $param->type == get_class($value));
    }

    public function style(string $name, ?Type $param, ?string $default = null): ?string
    {
        if ($param) {
            if (isset($param->{$name})) {
                return $param->{$name};
            }
            if (isset($param->rules) && isset($param->rules[$name])) {
                return $param->rules[$name];
            }
        }
        return $this->forms->style[$name] ?? $default;
    }

    /**
     * @param Param $p
     *
     * @param mixed $value
     * @param bool $dataOnly
     * @return array|T
     */
    public function field(Param $p, $value, bool $dataOnly = false)
    {
        if (is_string($value)) {
            //prevent XSS attacks
            $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, 'UTF-8');
        }
        $type = $p->field ?: static::guessFieldType($p);
        $tag = in_array($type, $this->inputTypes)
            ? 'input' : $type;
        $options = [];
        $name = $p->name;
        $multiple = null;
        if ($p->type == 'array' && $p->contentType != 'associative') {
            $name .= '[]';
            $multiple = true;
        }
        if ($p->choice) {
            foreach ($p->choice as $i => $choice) {
                $option = ['name' => $name, 'value' => $choice];
                $option['text'] = $p->rules['select'][$i] ?? $choice;
                if ($choice == $value) {
                    $option['selected'] = true;
                }
                $options[] = $option;
            }
        } elseif ($p->type == 'boolean' || $p->type == 'bool') {
            if (Text::beginsWith($type, 'radio') || Text::beginsWith($type, 'select')) {
                $options[] = [
                    'name' => $p->name,
                    'text' => ' Yes ',
                    'value' => 'true'
                ];
                $options[] = [
                    'name' => $p->name,
                    'text' => ' No ',
                    'value' => 'false'
                ];
                if ($value || $p->default[1]) {
                    $options[0]['selected'] = true;
                }
            } else { //checkbox
                $r = [
                    'tag' => $tag,
                    'name' => $name,
                    'type' => $type,
                    'label' => $p->label,
                    'value' => 'true',
                    'default' => $p->default[1],
                ];
                $r['text'] = 'Yes';
                if ($p->default[1]) {
                    $r['selected'] = true;
                }
                if (isset($p->rules)) {
                    $r += $p->rules;
                }
            }
        }
        if (empty($r)) {
            $r = [
                'tag' => $tag,
                'name' => $name,
                'type' => $type,
                'label' => $p->label,
                'value' => $value,
                'default' => $p->default[1],
                'options' => & $options,
                'multiple' => $multiple,
            ];
            if (isset($p->rules)) {
                $r += $p->rules;
            }
        }
        if ('file' == $type) {
            $this->fileUpload = true;
            if (empty($r['accept'])) {
                $r['accept'] = implode(', ', Upload::supportedMediaTypes());
            }
        }
        if (!empty(Validator::$exceptions[$name]) && $this->route->url == $this->restler->path) {
            $r['error'] = 'has-error';
            $r['message'] = Validator::$exceptions[$p->name]->getMessage();
        }

        if (true === $p->required) {
            $r['required'] = 'required';
        }
        if (isset($p->rules['autofocus'])) {
            $r['autofocus'] = 'autofocus';
        }
        /*
        echo "<pre>";
        print_r($r);
        echo "</pre>";
        */
        if ($dataOnly) {
            return $r;
        }
        if (isset($p->rules['form'])) {
            return Emmet::make($p->rules['form'], $r);
        }
        $t = Emmet::make($this->style($type, $p) ?: $this->style($tag, $p), $r);
        return $t;
    }

    protected function guessFieldType(Param $p, $type = 'type'): string
    {
        if (in_array($p->$type, $this->inputTypes)) {
            return $p->$type;
        }
        if ($p->choice) {
            return $p->type == 'array' ? 'checkbox' : 'select';
        }
        switch ($p->$type) {
            case 'boolean':
                return 'radio';
            case 'int':
            case 'number':
            case 'float':
                return 'number';
            case UploadedFileInterface::class:
                return 'file';
            case 'array':
                return $this->guessFieldType($p, 'contentType');
        }
        if ($p->name == 'password') {
            return 'password';
        }
        return 'text';
    }

    /**
     * Get the form key
     *
     * @param string $method http method for form key
     * @param string|null $action relative path from the web root. When set to null
     *                         it uses the current api method's path
     *
     * @return string generated form key
     */
    public function key(string $method = 'POST', string $action = null): string
    {
        if (is_null($action)) {
            $action = $this->restler->path;
        }
        $target = "$method $action";
        if (empty($this->key[$target])) {
            $this->key[$target] = md5($target . $this->userIdentifier->getCacheIdentifier() . uniqid(mt_rand()));
        }
        $this->session->set(static::FORM_KEY, $this->key);
        return $this->key[$target];
    }

    /**
     * Access verification method.
     *
     * API access will be denied when this method returns false
     *
     * @param ServerRequestInterface $request
     * @param UserIdentificationInterface $userIdentifier
     * @param ResponseHeaders $responseHeaders
     *
     * @return bool true when api access is allowed false otherwise
     *
     * @throws HttpException 403 security violation
     */
    public function _isAllowed(
        ServerRequestInterface $request,
        UserIdentificationInterface $userIdentifier,
        ResponseHeaders $responseHeaders
    ): bool {
        $this->userIdentifier = $userIdentifier;
        if ('GET' === $request->getMethod()) {
            return true;
        }
        if ($this->session->getId() == '') {
            $this->session->start();
        }
        $restler = $this->restler;
        $url = $restler->path;
        $check = !static::$filterFormRequestsOnly
            || $restler->requestFormat instanceof UrlEncoded
            || $restler->requestFormat instanceof Upload;
        $post = $request->getParsedBody();
        if (!empty($post) && $check) {
            if (
                isset($post[static::FORM_KEY]) &&
                ($target = $restler->requestMethod . ' ' . $restler->path) &&
                $this->session->has(static::FORM_KEY) &&
                $post[static::FORM_KEY] == $this->session->get(static::FORM_KEY)[$target]
            ) {
                return true;
            }
            throw new HttpException(403, 'Insecure form submission');
        }
        return true;
    }
}
