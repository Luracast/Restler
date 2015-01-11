<?php

/* oauth2/analytics.twig */
class __TwigTemplate_b44fd31d4fe40b2a78ec7d0795f97050 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        if (array_key_exists("googleAnalyticsCode", $context)) {
            // line 2
            echo "<script type=\"text/javascript\">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '";
            // line 5
            if (isset($context["googleAnalyticsCode"])) { $_googleAnalyticsCode_ = $context["googleAnalyticsCode"]; } else { $_googleAnalyticsCode_ = null; }
            echo twig_escape_filter($this->env, $_googleAnalyticsCode_, "html", null, true);
            echo "']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
";
        }
    }

    public function getTemplateName()
    {
        return "oauth2/analytics.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  26 => 5,  21 => 2,  19 => 1,);
    }
}
