<?php

/* oauth2/client/error.twig */
class __TwigTemplate_2d15e3b8c6e6e607ed962c1b6ee11ea4 extends Twig_Template
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
        echo "    <h3>Authorization Error</h3>
    <p>
        ";
        // line 6
        if (isset($context["response"])) { $_response_ = $context["response"]; } else { $_response_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_response_, "error"), "error_description"), "html", null, true);
        echo "
        ";
        // line 7
        if (isset($context["response"])) { $_response_ = $context["response"]; } else { $_response_ = null; }
        if ($this->getAttribute($this->getAttribute($_response_, "error", array(), "any", false, true), "error_uri", array(), "any", true, true)) {
            // line 8
            echo "            (<a href=\"";
            if (isset($context["response"])) { $_response_ = $context["response"]; } else { $_response_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_response_, "error"), "error_uri"), "html", null, true);
            echo "\">more information</a>)
        ";
        }
        // line 10
        echo "    </p>
    <a href=\"";
        // line 11
        if (isset($context["basePath"])) { $_basePath_ = $context["basePath"]; } else { $_basePath_ = null; }
        echo twig_escape_filter($this->env, $_basePath_, "html", null, true);
        echo "\">back</a>
";
    }

    public function getTemplateName()
    {
        return "oauth2/client/error.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  53 => 11,  50 => 10,  43 => 8,  40 => 7,  35 => 6,  31 => 4,  28 => 3,);
    }
}
