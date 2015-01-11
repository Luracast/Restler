<?php

/* oauth2/client/granted.twig */
class __TwigTemplate_d753436d42c931cd6e479413de833ab0 extends Twig_Template
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
        echo "    <h3>Authorization Granted!</h3>
    <pre><code>  Access Token: ";
        // line 5
        if (isset($context["response"])) { $_response_ = $context["response"]; } else { $_response_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_response_, "token"), "html", null, true);
        echo "  </code></pre>
    <p>
        Here are your friends:
    </p>
    <ul>
        ";
        // line 10
        if (isset($context["response"])) { $_response_ = $context["response"]; } else { $_response_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($_response_, "friends"));
        foreach ($context['_seq'] as $context["_key"] => $context["friend"]) {
            // line 11
            echo "            <li>";
            if (isset($context["friend"])) { $_friend_ = $context["friend"]; } else { $_friend_ = null; }
            echo twig_escape_filter($this->env, $_friend_, "html", null, true);
            echo "</li>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['friend'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 13
        echo "    </ul>
    <div class=\"help\"><em>The API call can be seen at <a href=\"";
        // line 14
        if (isset($context["response"])) { $_response_ = $context["response"]; } else { $_response_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_response_, "endpoint"), "html", null, true);
        echo "\" target=\"_blank\">";
        if (isset($context["response"])) { $_response_ = $context["response"]; } else { $_response_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_response_, "endpoint"), "html", null, true);
        echo "</a></em></div>
";
    }

    public function getTemplateName()
    {
        return "oauth2/client/granted.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  61 => 14,  58 => 13,  48 => 11,  43 => 10,  34 => 5,  31 => 4,  28 => 3,);
    }
}
