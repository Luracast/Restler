<?php


namespace Luracast\Restler\Middleware;


use Exception;
use HansOtt\PSR7Cookies\SetCookie;
use Luracast\Restler\Contracts\ContainerInterface;
use Luracast\Restler\Contracts\MiddlewareInterface;
use Luracast\Restler\Contracts\SessionInterface;
use Luracast\Restler\Defaults;
use Luracast\Restler\Session;
use Luracast\Restler\Session\FileSessionHandler;
use Psr\Http\Message\ServerRequestInterface;
use SessionHandlerInterface;
use SessionIdInterface;

use function React\Promise\resolve;
use function time;

class SessionMiddleware implements MiddlewareInterface
{
    public static string $cookieName = 'RESTLER_SESSION';

    public static array $cookieParameters = [
        0,
        '',
        '',
        false,
        false,
    ];
    /**
     * @var SessionHandlerInterface
     */
    private ?\SessionHandlerInterface $handler;
    /**
     * @var SessionIdInterface
     */
    private ?\SessionIdInterface $sessionId;

    /**
     * SessionMiddleware constructor.
     * @param SessionHandlerInterface|null $handler
     * @param SessionIdInterface|null $sessionId
     * @throws Exception
     */
    public function __construct(SessionHandlerInterface $handler = null, SessionIdInterface $sessionId = null)
    {
        if ($handler) {
            $this->handler = $handler;
        } else {
            $this->handler = new FileSessionHandler(Defaults::$cacheDirectory . '/sessions');
        }
        if ($sessionId) {
            $this->sessionId = $sessionId;
        } elseif ($this->handler instanceof SessionIdInterface) {
            $this->sessionId = $this->handler;
        } else {
            throw new Exception("SessionIdInterface is needed.");
        }
    }

    public function __invoke(
        ServerRequestInterface $request,
        callable $next = null,
        ContainerInterface $container = null
    ) {
        $id = $request->getCookieParams()[static::$cookieName] ?? '';
        $session = new Session($this->handler, $this->sessionId, $id);
        if ($container) {
            $container->instance(SessionInterface::class, $session);
        } else {
            $request = $request->withAttribute('session', $session);
        }
        return resolve($next($request))->then(
            function ($response) use ($session): \Psr\Http\Message\ResponseInterface {
                $cookieParams = static::$cookieParameters;
                // Only set time when expires is set in the future
                if ($cookieParams[0] > 0) {
                    $cookieParams[0] += time();
                }
                if ($session->status() == PHP_SESSION_ACTIVE) {
                    $cookie = new SetCookie(static::$cookieName, $session->getId(), ...$cookieParams);
                    $session->commit();
                    return $cookie->addToResponse($response);
                }
                array_shift($cookieParams);
                $cookie = SetCookie::thatDeletesCookie(static::$cookieName, ...$cookieParams);
                $session->destroy();
                return $cookie->addToResponse($response);
            }
        );
    }
}
