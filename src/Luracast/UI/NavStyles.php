<?php


namespace Luracast\Restler\UI;


class NavStyles
{
    public static array $bootstrap3 = [
        'root' => 'ul.nav.nav-tabs',
        'leaf' => 'li[role=presentation]>a[href=$href#]{$text#}',
        'branch' => 'li.dropdown[role=presentation]>a.dropdown-toggle[data-toggle=dropdown href=$href# role=button aria-haspopup=true aria-expanded=false]{$text# }>span.caret^ul.dropdown-menu>li*children>a[href=$href#]{$text#}',
        'tabs' => 'ul.nav.nav-tabs',
        'tabs-justified' => 'ul.nav.nav-tabs.nav-justified',
        'pills' => 'ul.nav.nav-pills',
        'pills-justified' => 'ul.nav.nav-pills.nav-justified',
        'pills-stacked' => 'ul.nav.nav-pills.nav-stacked',
    ];
}
