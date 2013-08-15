<?php

/* oauth2/client/index.twig */
class __TwigTemplate_e09188a66f88d48558825932c7326987 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("oauth2/client/base.twig");

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "oauth2/client/base.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = array())
    {
        // line 4
        echo "    <h3>Demo App</h3>
    <p>
        We would like to use your information in order to integrate with your friends,
        use your personal information for nefarious purposes, and to make your life better somehow.
    </p>
    <p>
        Click below to integrate with that service you belong to:
    </p>
    <a class=\"button\" href=\"";
        // line 12
        if (isset($context["response"])) { $_response_ = $context["response"]; } else { $_response_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_response_, "authorize_url"), "html", null, true);
        echo "?response_type=code&client_id=demoapp&redirect_uri=";
        if (isset($context["response"])) { $_response_ = $context["response"]; } else { $_response_ = null; }
        echo twig_escape_filter($this->env, twig_urlencode_filter($this->getAttribute($_response_, "authorize_redirect_url")), "html", null, true);
        echo "\">Authorize</a>
";
    }

    public function getTemplateName()
    {
        return "oauth2/client/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  41 => 12,  31 => 4,  28 => 3,);
    }
}
