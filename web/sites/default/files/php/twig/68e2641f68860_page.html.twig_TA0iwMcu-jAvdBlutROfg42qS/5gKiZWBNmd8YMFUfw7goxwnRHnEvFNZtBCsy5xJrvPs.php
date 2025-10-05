<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* themes/custom/ncktrnr_tw/templates/layout/page.html.twig */
class __TwigTemplate_14b94004c9ca3f5025a614eeaa09b394 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->extensions[SandboxExtension::class];
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 45
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->attachLibrary("ncktrnr_tw/logo_nav"), "html", null, true);
        yield "
<div class=\"layout-container\">

  <header role=\"banner\" class=\"mb-12\">
    ";
        // line 49
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "header", [], "any", false, false, true, 49), "html", null, true);
        yield "

    ";
        // line 51
        yield from $this->load("@ncktrnr_tw/includes/logo-nav.html.twig", 51)->unwrap()->yield($context);
        // line 52
        yield "
  </header>

  ";
        // line 55
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "primary_menu", [], "any", false, false, true, 55), "html", null, true);
        yield "
  ";
        // line 56
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "secondary_menu", [], "any", false, false, true, 56), "html", null, true);
        yield "

  ";
        // line 58
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "breadcrumb", [], "any", false, false, true, 58), "html", null, true);
        yield "

  ";
        // line 60
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "highlighted", [], "any", false, false, true, 60), "html", null, true);
        yield "

  ";
        // line 62
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "help", [], "any", false, false, true, 62), "html", null, true);
        yield "

  <main role=\"main\">
    <a id=\"main-content\" tabindex=\"-1\"></a>";
        // line 66
        yield "
    <div class=\"layout-content container px-4 lg:px-0\">
      ";
        // line 68
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "content", [], "any", false, false, true, 68), "html", null, true);
        yield "
    </div>";
        // line 70
        yield "
    ";
        // line 71
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_first", [], "any", false, false, true, 71)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 72
            yield "      <aside class=\"layout-sidebar-first\" role=\"complementary\">
        ";
            // line 73
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_first", [], "any", false, false, true, 73), "html", null, true);
            yield "
      </aside>
    ";
        }
        // line 76
        yield "
    ";
        // line 77
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_second", [], "any", false, false, true, 77)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 78
            yield "      <aside class=\"layout-sidebar-second\" role=\"complementary\">
        ";
            // line 79
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "sidebar_second", [], "any", false, false, true, 79), "html", null, true);
            yield "
      </aside>
    ";
        }
        // line 82
        yield "
  </main>

  ";
        // line 85
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "footer", [], "any", false, false, true, 85)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 86
            yield "    <footer role=\"contentinfo\" class=\"border-t-2 border-neutral-600 min-h-24 relative\">
    <div class=\"absolute -top-17 right-10\">
      ";
            // line 88
            yield from $this->load("@ncktrnr_tw/images/dino-vegan.svg", 88)->unwrap()->yield($context);
            // line 89
            yield "    </div>
      ";
            // line 90
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "footer", [], "any", false, false, true, 90), "html", null, true);
            yield "
    </footer>
  ";
        }
        // line 93
        yield "
</div>";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["page"]);        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "themes/custom/ncktrnr_tw/templates/layout/page.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  146 => 93,  140 => 90,  137 => 89,  135 => 88,  131 => 86,  129 => 85,  124 => 82,  118 => 79,  115 => 78,  113 => 77,  110 => 76,  104 => 73,  101 => 72,  99 => 71,  96 => 70,  92 => 68,  88 => 66,  82 => 62,  77 => 60,  72 => 58,  67 => 56,  63 => 55,  58 => 52,  56 => 51,  51 => 49,  44 => 45,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/custom/ncktrnr_tw/templates/layout/page.html.twig", "/app/web/themes/custom/ncktrnr_tw/templates/layout/page.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = ["include" => 51, "if" => 71];
        static $filters = ["escape" => 45];
        static $functions = ["attach_library" => 45];

        try {
            $this->sandbox->checkSecurity(
                ['include', 'if'],
                ['escape'],
                ['attach_library'],
                $this->source
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
