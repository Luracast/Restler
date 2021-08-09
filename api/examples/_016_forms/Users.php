<?php

use Luracast\Restler\StaticProperties;
use Luracast\Restler\UI\FormStyles;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Users
 * @response-format Html,Json
 */
class Users
{
    public function __construct(ServerRequestInterface $request, StaticProperties $html, StaticProperties $forms)
    {
        $bootstrap4 = [
            'cerulean',
            'cosmo',
            'cyborg',
            'darkly',
            'flatly',
            'journal',
            'litera',
            'lumen',
            'lux',
            'materia',
            'minty',
            'pulse',
            'sandstone',
            'simplex',
            'sketchy',
            'slate',
            'solar',
            'spacelab',
            'superhero',
            'united',
            'yeti',
        ];
        $bootstrap3 = [
            'cerulean',
            'cosmo',
            'cyborg',
            'darkly',
            'flatly',
            'journal',
            'lumen',
            'paper',
            'readable',
            'sandstone',
            'simplex',
            'slate',
            'spacelab',
            'superhero',
            'united',
            'yeti',
        ];
        $theme = $request->getQueryParams()['theme'] ?? 'bootstrap4-' . $bootstrap4[array_rand($bootstrap4, 1)];
        [$style, $theme] = explode('-', $theme, 2);
        $html->data['theme'] = $theme;
        $html->data['themes'] = [
            ['name' => 'bootstrap3', 'items' => $bootstrap3],
            ['name' => 'bootstrap4', 'items' => $bootstrap4],
            ['name' => 'foundation5', 'noitems' => true],
        ];
        $html->data['bootstrap3'] = $bootstrap3;
        $html->data['bootstrap4'] = $bootstrap4;
        $html->data['style'] = $style;
        $html->data['isBootstrap3'] = $style === 'bootstrap3';
        $html->data['isBootstrap4'] = $style === 'bootstrap4';
        $html->data['isFoundation5'] = $theme === 'foundation5';
        $forms->style = FormStyles::$$style;
    }

    function index()
    {
        return [];
    }

    /**
     * @param string $email
     * @param string $password
     * @return array {@label <span class="glyphicon glyphicon-user"></span> Sign In}
     */
    function postSignIn(string $email, string $password): array
    {
        return func_get_args();
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $password
     * @param Address $address
     *
     * @return array
     *
     * @view users
     */
    function postSignUp(string $firstName, string $lastName, string $email, string $password, Address $address): array
    {
        return func_get_args();
    }
}
