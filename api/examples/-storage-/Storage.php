<?php


use Luracast\Restler\Defaults;
use Luracast\Restler\Restler;
use Luracast\Restler\Routes;
use Luracast\Restler\Utils\ClassName;
use Luracast\Restler\Utils\Text;
use Psr\Http\Message\ServerRequestInterface;

/**
 * api for resetting various storage so that tests can run in expected state.
 */
class Storage
{

    /**
     * @var Restler
     */
    private $r;
    /**
     * @var ServerRequestInterface
     */
    private $s;

    public function __construct(Restler $r, ServerRequestInterface $s)
    {
        $this->r = $r;
        $this->s = $s;
    }

    public function baseUrl()
    {
        return [
            'BASE_URL' => (string)$this->r->baseUrl,
            'BASE_PATH' => (string)$this->r->baseUrl->getPath(),
            'SCRIPT_NAME' => $this->s->getServerParams()['SCRIPT_NAME'],
            'REQUEST_URI' => $this->s->getServerParams()['REQUEST_URI'],
            'PATH' => $this->r->path,
            'FULL_URL' => (string)$this->s->getUri(),
            'FULL_PATH' => (string)$this->s->getUri()->getPath(),
        ];
    }

    public function routes()
    {
        return Routes::toArray();
    }

    public function pack()
    {
        if (Text::contains(BASE, 'private')) {
            return [];
        }
        //make sure the following classes are added
        class_exists(Symfony\Polyfill\Mbstring\Mbstring::class);
        class_exists(React\Promise\RejectedPromise::class);
        class_exists(ClassName::get('HttpClientInterface'));
        $assets = [
            'src/OpenApi3/client/index.html',
            'src/OpenApi3/client/oauth2-redirect.html',
        ];
        $files = get_included_files();
        $targets = [];
        foreach ($files as $file) {
            if (Text::beginsWith($file, '/private/') || Text::beginsWith($file, Defaults::$cacheDirectory)) {
                continue;
            }
            $base = str_replace(BASE . DIRECTORY_SEPARATOR, '', $file);
            $target = Defaults::$cacheDirectory . '/package/' . $base;
            $dir = dirname($target);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            copy($file, $target);
            $targets[] = $base;
        }
        foreach ($assets as $base) {
            $file = BASE . DIRECTORY_SEPARATOR . $base;
            $target = Defaults::$cacheDirectory . '/package/' . $base;
            $dir = dirname($target);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            copy($file, $target);
            $targets[] = $base;
        }
        $pack = function ($dir) {
            $parent = dirname($dir);
            $parent = '.' == $parent ? '' : $parent . '/';
            $command = sprintf(
                'cp -R "%s/%s" "%s/package/%s"',
                BASE,
                $dir,
                Defaults::$cacheDirectory,
                $parent
            );
            return exec($command);
        };
        $pack('views');
        $pack('src/views');
        $pack('public');
        if (file_exists($file = Defaults::$cacheDirectory . '/package/bootstrap')) {
            exec('chmod +x "' . $file . '"');
        }
        return $targets;
    }

    /**
     * Delete session, cache, databases
     *
     * Removes the cache files to begin testing on a clean slate
     */
    public function deleteAll()
    {
        $this->deleteCache();
        $this->deletePackage();
        $this->deleteSessions();
        $this->resetDatabases();
    }

    private function deleteCache()
    {
        //template
        @exec('rm -rf ' . Defaults::$cacheDirectory . '/blade');
        @exec('rm -rf ' . Defaults::$cacheDirectory . '/twig');
        @exec('rm -rf ' . Defaults::$cacheDirectory . '/php');
        //rate limit and other cache
        @exec('rm ' . Defaults::$cacheDirectory . '/*.php');
    }

    private function deletePackage()
    {
        @exec('rm -rf ' . Defaults::$cacheDirectory . '/package');
    }

    private function deleteSessions()
    {
        $path = Defaults::$cacheDirectory . '/sessions';
        @exec('rm -rf ' . $path);
        mkdir($path, 0777, true);
    }

    private function resetDatabases()
    {
        $class = ClassName::get(DataProviderInterface::class);
        $class::reset();
    }
}
