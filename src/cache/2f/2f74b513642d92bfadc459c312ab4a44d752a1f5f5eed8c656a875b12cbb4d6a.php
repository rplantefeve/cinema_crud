<?php

/* base.html.twig */
class __TwigTemplate_92aa99c9b74628141559768d0192f8810156b7fa3d74887414ba06bcd2195629 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 2
        echo "<!DOCTYPE html>
<html>
    <head>
        <meta charset=\"UTF-8\">
        <title>";
        // line 6
        $this->displayBlock('title', $context, $blocks);
        echo "</title>
        <link type=\"text/css\" href=\"";
        // line 7
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("/css/cinema.css"), "html", null, true);
        echo "\" rel=\"stylesheet\"/>
    </head>
    <body>
        <div id=\"content\">";
        // line 11
        $this->displayBlock('content', $context, $blocks);
        // line 12
        echo "        </div>
    </body>
</html>

";
    }

    // line 6
    public function block_title($context, array $blocks = array())
    {
    }

    // line 11
    public function block_content($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "base.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  52 => 11,  47 => 6,  39 => 12,  37 => 11,  31 => 7,  27 => 6,  21 => 2,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# Base Twig template #}
<!DOCTYPE html>
<html>
    <head>
        <meta charset=\"UTF-8\">
        <title>{% block title %}{% endblock %}</title>
        <link type=\"text/css\" href=\"{{ asset('/css/cinema.css') }}\" rel=\"stylesheet\"/>
    </head>
    <body>
        <div id=\"content\">
            {% block content %}{% endblock %}
        </div>
    </body>
</html>

", "base.html.twig", "D:\\Users\\Romain\\source\\repos\\PHP\\cinema_crud\\src\\views\\base.html.twig");
    }
}
