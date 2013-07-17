<?php

/* oauth2/server/base.twig */
class __TwigTemplate_cb4f942073e2d6724b0da6ca1efcaeac extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
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
        echo "/css/lockdin.css\">
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
        echo "        ";
        $this->env->loadTemplate("oauth2/server/header.html")->display($context);
        // line 24
        echo "
        <div id=\"container\">
            <article class=\"home\" role=\"main\">
                <div id=\"content\" role=\"main\">
                  ";
        // line 28
        $this->displayBlock('content', $context, $blocks);
        // line 30
        echo "                </div>
            </article>
        </div>
    </body>
</html>
";
    }

    // line 28
    public function block_content($context, array $blocks = array())
    {
        // line 29
        echo "                  ";
    }

    public function getTemplateName()
    {
        return "oauth2/server/base.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  78 => 29,  75 => 28,  66 => 30,  64 => 28,  58 => 24,  55 => 23,  52 => 22,  39 => 14,  34 => 13,  20 => 1,  61 => 28,  50 => 21,  31 => 4,  28 => 3,);
    }
}
