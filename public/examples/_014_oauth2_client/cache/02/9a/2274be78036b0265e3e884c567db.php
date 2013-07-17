<?php

/* oauth2/client/base.twig */
class __TwigTemplate_029a2274be78036b0265e3e884c567db extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'header' => array($this, 'block_header'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<!--[if lt IE 7]>      <html class=\"no-js lt-ie9 lt-ie8 lt-ie7\"> <![endif]-->
<!--[if IE 7]>         <html class=\"no-js lt-ie9 lt-ie8\"> <![endif]-->
<!--[if IE 8]>         <html class=\"no-js lt-ie9\"> <![endif]-->
<!--[if gt IE 8]><!--> <html class=\"no-js\"> <!--<![endif]-->
    <head>
        <meta charset=\"utf-8\">
        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\">
        <title></title>
        <meta name=\"description\" content=\"\">
        <meta name=\"viewport\" content=\"width=device-width\">

        <link rel=\"stylesheet\" href=\"";
        // line 13
        if (isset($context["basePath"])) { $_basePath_ = $context["basePath"]; } else { $_basePath_ = null; }
        echo twig_escape_filter($this->env, $_basePath_, "html", null, true);
        echo "/css/demo.css\">
        <link rel=\"stylesheet\" href=\"";
        // line 14
        if (isset($context["basePath"])) { $_basePath_ = $context["basePath"]; } else { $_basePath_ = null; }
        echo twig_escape_filter($this->env, $_basePath_, "html", null, true);
        echo "/css/shared.css\">
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class=\"chromeframe\">You are using an outdated browser. <a href=\"http://browsehappy.com/\">Upgrade your browser today</a> or <a href=\"http://www.google.com/chromeframe/?redirect=true\">install Google Chrome Frame</a> to better experience this site.</p>
        <![endif]-->

        ";
        // line 21
        $this->env->loadTemplate("oauth2/analytics.twig")->display($context);
        // line 22
        echo "        ";
        $this->env->loadTemplate("oauth2/github.twig")->display($context);
        // line 23
        echo "
        <div id=\"container\">
            <header role=\"banner\">
              ";
        // line 26
        $this->displayBlock('header', $context, $blocks);
        // line 51
        echo "            </header>

            <article class=\"home\" role=\"main\">
                <div  role=\"main\">
                  ";
        // line 55
        $this->displayBlock('content', $context, $blocks);
        // line 57
        echo "                </div>
            </article>
        </div>
    </body>
</html>
";
    }

    // line 26
    public function block_header($context, array $blocks = array())
    {
        // line 27
        echo "              <hgroup>
                  <h1>Restler 3</h1>
                  <h2>OAuth 2 Demo App</h2>
              </hgroup>
                  <nav class=\"primary\">
                      <ul class=\"menu\">
                          <li class=\"current\">
                              <a href=\"http://luracast.com\">Home</a>
                          </li>
                          <li>
                              <a href=\"../\">More</a>
                          </li>
                          <li>
                              <a href=\"http://github.com/Luracast\">Source</a>
                          </li>
                          <li>
                              <a href=\"http://twitter.com/Luracast\">Twitter</a>
                          </li>
                          <li>
                              <a href=\"http://github.com/bshaffer\">OAuth2</a>
                          </li>
                      </ul>
                  </nav>
              ";
    }

    // line 55
    public function block_content($context, array $blocks = array())
    {
        // line 56
        echo "                  ";
    }

    public function getTemplateName()
    {
        return "oauth2/client/base.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  113 => 56,  110 => 55,  83 => 27,  80 => 26,  71 => 57,  69 => 55,  63 => 51,  61 => 26,  56 => 23,  53 => 22,  51 => 21,  40 => 14,  35 => 13,  21 => 1,);
    }
}
