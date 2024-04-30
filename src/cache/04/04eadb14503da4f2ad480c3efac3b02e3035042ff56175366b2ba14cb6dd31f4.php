<?php

/* index.html.twig */
class __TwigTemplate_48260990193579ceb12add52d31e9eaf375bc18a5be519e26f0968d4508944e5 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("base.html.twig", "index.html.twig", 1);
        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = array())
    {
        echo twig_escape_filter($this->env, ($context["titre"] ?? $this->getContext($context, "titre")), "html", null, true);
    }

    // line 5
    public function block_content($context, array $blocks = array())
    {
        // line 6
        echo "    <div>
        <header>
            <h1>";
        // line 8
        echo twig_escape_filter($this->env, ($context["titre"] ?? $this->getContext($context, "titre")), "html", null, true);
        echo "</h1>
        </header>";
        // line 10
        if ((($context["loginSuccess"] ?? $this->getContext($context, "loginSuccess")) == false)) {
            // line 11
            echo "            <form method=\"post\" name=\"editFavoriteMoviesList\" action=\"";
            echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("login");
            echo "\">

                <label>Adresse email : </label>
                <input type=\"email\" name=\"email\" value=\"";
            // line 14
            echo twig_escape_filter($this->env, ($context["email"] ?? $this->getContext($context, "email")), "html", null, true);
            echo "\" required/>
                <label>Mot de passe  : </label>
                <input type=\"password\" name=\"password\" required/>
                <div class=\"error\">";
            // line 18
            if ((($context["errorMessage"] ?? $this->getContext($context, "errorMessage")) != false)) {
                // line 19
                echo twig_escape_filter($this->env, ($context["errorMessage"] ?? $this->getContext($context, "errorMessage")), "html", null, true);
            }
            // line 21
            echo "                </div>
                <input type=\"submit\" value=\"Editer ma liste de films préférés\"/>
            </form>
            <p>Pas encore d'espace personnel ? <a href=\"";
            // line 24
            echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("user_add");
            echo "\">Créer sa liste de films préférés.</a></p>";
        } else {
            // line 26
            echo "            <form action=\"";
            echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("favorite_list");
            echo "\">
                <input type=\"submit\" value=\"Editer ma liste de films préférés\"/>
            </form>
            <a href=\"";
            // line 29
            echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("logout");
            echo "\">Se déconnecter</a>";
        }
        // line 31
        echo "    </div>
    <!-- Gestion des cinémas -->
    <div>
        <header>
            <h1>Gestion des cinémas</h1>
            <form name=\"cinemasList\" method=\"GET\" action=\"";
        // line 36
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("cinema_list");
        echo "\">
                <input type=\"submit\" value=\"Consulter la liste des cinémas\"/>
            </form>
            <form name=\"moviesList\" method=\"GET\" action=\"";
        // line 39
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("movie_list");
        echo "\">
                <input type=\"submit\" value=\"Consulter la liste des films\"/>
            </form>
        </header>
    </div>";
    }

    public function getTemplateName()
    {
        return "index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  99 => 39,  93 => 36,  86 => 31,  82 => 29,  75 => 26,  71 => 24,  66 => 21,  63 => 19,  61 => 18,  55 => 14,  48 => 11,  46 => 10,  42 => 8,  38 => 6,  35 => 5,  29 => 3,  11 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% extends \"base.html.twig\" %}

{% block title %}{{ titre }}{% endblock %}

{% block content %}
    <div>
        <header>
            <h1>{{ titre }}</h1>
        </header>
        {% if loginSuccess == false %}
            <form method=\"post\" name=\"editFavoriteMoviesList\" action=\"{{ path('login') }}\">

                <label>Adresse email : </label>
                <input type=\"email\" name=\"email\" value=\"{{ email }}\" required/>
                <label>Mot de passe  : </label>
                <input type=\"password\" name=\"password\" required/>
                <div class=\"error\">
                    {% if errorMessage != false %}
                        {{ errorMessage }}
                    {% endif %}
                </div>
                <input type=\"submit\" value=\"Editer ma liste de films préférés\"/>
            </form>
            <p>Pas encore d'espace personnel ? <a href=\"{{ path('user_add') }}\">Créer sa liste de films préférés.</a></p>
        {% else %}
            <form action=\"{{ path('favorite_list') }}\">
                <input type=\"submit\" value=\"Editer ma liste de films préférés\"/>
            </form>
            <a href=\"{{ path('logout') }}\">Se déconnecter</a>
        {% endif %}
    </div>
    <!-- Gestion des cinémas -->
    <div>
        <header>
            <h1>Gestion des cinémas</h1>
            <form name=\"cinemasList\" method=\"GET\" action=\"{{ path('cinema_list') }}\">
                <input type=\"submit\" value=\"Consulter la liste des cinémas\"/>
            </form>
            <form name=\"moviesList\" method=\"GET\" action=\"{{ path('movie_list') }}\">
                <input type=\"submit\" value=\"Consulter la liste des films\"/>
            </form>
        </header>
    </div>    
{% endblock %}
", "index.html.twig", "D:\\Users\\Romain\\source\\repos\\PHP\\cinema_crud\\src\\views\\index.html.twig");
    }
}
