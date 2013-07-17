<?php

/* oauth2/client/denied.twig */
class __TwigTemplate_42e2b33d0a0c2ab52c824b712172e49d extends Twig_Template
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
        echo "    <h3>Authorization Failed!</h3>
    <p>
        It seems authorization has been denied for the following reasons:
    </p>
    <ul>
        <li>
            ";
        // line 10
        if (isset($context["response"])) { $_response_ = $context["response"]; } else { $_response_ = null; }
        if ($this->getAttribute($this->getAttribute($_response_, "error"), "error_description")) {
            // line 11
            echo "                ";
            if (isset($context["response"])) { $_response_ = $context["response"]; } else { $_response_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_response_, "error"), "error_description"), "html", null, true);
            echo "
                ";
            // line 13
            echo "                ";
            if (isset($context["response"])) { $_response_ = $context["response"]; } else { $_response_ = null; }
            if ($this->getAttribute($_response_, "error_uri")) {
                // line 14
                echo "                    (<a href=\"";
                if (isset($context["response"])) { $_response_ = $context["response"]; } else { $_response_ = null; }
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($_response_, "error"), "error_uri"), "html", null, true);
                echo "\">more information</a>)
                ";
            }
            // line 16
            echo "            ";
        } else {
            // line 17
            echo "                <em>authorization server did not supply an error message</em>
            ";
        }
        // line 19
        echo "        </li>
    </ul>
";
    }

    public function getTemplateName()
    {
        return "oauth2/client/denied.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  66 => 19,  62 => 17,  59 => 16,  52 => 14,  48 => 13,  42 => 11,  39 => 10,  31 => 4,  28 => 3,);
    }
}
