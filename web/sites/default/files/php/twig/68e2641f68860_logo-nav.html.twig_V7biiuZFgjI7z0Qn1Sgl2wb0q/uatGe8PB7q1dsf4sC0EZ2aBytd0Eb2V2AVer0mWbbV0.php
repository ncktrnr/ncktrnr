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

/* @ncktrnr_tw/includes/logo-nav.html.twig */
class __TwigTemplate_d396b099895d48fa6d6cfea556cf50c7 extends Template
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
        // line 1
        yield "

<div class=\"container p-4\">
  <nav class=\"primary-nav\" role=\"navigation\" aria-label=\"Primary\"> 
    <a href=\"";
        // line 5
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar($this->extensions['Drupal\Core\Template\TwigExtension']->getPath("<front>"));
        yield "\" class=\"logo js-logo inline-flex items-center select-none whitespace-nowrap overflow-hidden focus-visible:outline focus-visible:outline-neutral-900 focus-visible:outline-offset-2 rounded py-2\" aria-label=\"Nick Turner\">  
        <span class=\"reveal leading-none\">
            <span class=\"track js-track\" aria-hidden=\"true\"> </span>
        </span> 
    </a> 
    <div class=\"flex items-center gap-7\"> 
      <a href=\"/work\" class=\"relative group/link focus-visible:outline focus-visible:outline-neutral-900 focus-visible:outline-offset-2 focus-visible:ring-offset-2 text-xl duration-200 hover:text-gray-600 active:text-gray-800 rounded transition-colors py-2\">work
        <span class=\"absolute bottom-1 left-0 w-0 h-0.5 bg-neutral-500 transition-all duration-300 ease-out group-hover/link:w-full motion-reduce:transition-none\"></span>
      </a> 
      <a href=\"/about\" class=\"relative group/link focus-visible:outline focus-visible:outline-neutral-900 focus-visible:outline-offset-2 focus-visible:ring-offset-2 text-xl duration-200 hover:text-gray-600 active:text-gray-800 rounded transition-colors py-2\">about
        <span class=\"absolute bottom-1 left-0 w-0 h-0.5 bg-neutral-500 transition-all duration-300 ease-out group-hover/link:w-full motion-reduce:transition-none\"></span>
      </a> 
    </div> 
  </nav>
</div>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "@ncktrnr_tw/includes/logo-nav.html.twig";
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
        return array (  50 => 5,  44 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "@ncktrnr_tw/includes/logo-nav.html.twig", "/app/web/themes/custom/ncktrnr_tw/templates/includes/logo-nav.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = [];
        static $filters = [];
        static $functions = ["path" => 5];

        try {
            $this->sandbox->checkSecurity(
                [],
                [],
                ['path'],
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
