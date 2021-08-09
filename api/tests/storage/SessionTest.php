<?php


use Luracast\Restler\Contracts\SessionInterface;

class SessionTest
{
    const KEY = 'session';
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $session->start();
        $this->session = $session;
    }

    public function get()
    {
        $value = null;
        if ($exists = $this->session->has(self::KEY)) {
            $exists = true;
            $value = $this->session->get(self::KEY);
        }
        return compact('exists', 'value');
    }

    public function post(string $value): bool
    {
        return $this->session->set(self::KEY, $value);
    }
}
