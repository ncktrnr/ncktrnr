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

/* @ncktrnr_tw/images/dino-vegan.svg */
class __TwigTemplate_305d2fd0bdb7f5e6ae4b1483a1b6b022 extends Template
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
        yield "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<svg id=\"fin\" xmlns=\"http://www.w3.org/2000/svg\" width=\"201\" height=\"77.5\" viewBox=\"0 0 201 77.5\">
    <defs>
        <style>.cls-1{fill:#525252;}</style>
    </defs>
    <path class=\"cls-1\"
        d=\"M169.91,51.74v5.15h20.6v-5.15h-10.3v-5.15h-5.15v-5.15h-5.15v-5.15h-5.15v-5.15h-5.15v-5.15h-10.3v-5.15h-25.75v5.15h-10.3v-10.3h-10.3v5.15h-10.3v-5.15h-5.15v-5.15h-10.3v5.15h5.15v5.15h5.15v5.15h-15.45v15.45h20.6v5.17h-15.45v5.15h15.45v-.02h10.3v5.15h10.3v10.3h-5.15v5.15h-5.15v5.15h15.45v-5.15h5.15v-10.3h5.15v-5.15h5.15v10.3h-5.15v5.15h15.45v-10.3h10.3v10.3h-5.15v5.15h10.3v-5.15h5.15v-10.3h-5.15v-5.15h-5.15v-5.15h15.45ZM87.51,36.29v-5.15h5.15v5.15h-5.15Z\" />
    <polygon class=\"cls-1\"
        points=\"52.23 46.23 57.38 46.23 57.38 20.47 52.23 20.47 52.23 15.32 47.08 15.32 47.08 41.07 41.93 41.07 41.93 5.15 36.78 5.15 36.78 0 26.48 0 26.48 51.88 21.33 51.88 21.33 26.12 16.18 26.12 16.18 31.27 11.03 31.27 11.03 57.03 16.18 57.03 16.18 62.18 26.48 62.18 26.48 77.27 41.93 77.27 41.93 51.38 52.23 51.38 52.23 46.23\" />
</svg>";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "@ncktrnr_tw/images/dino-vegan.svg";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  44 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "@ncktrnr_tw/images/dino-vegan.svg", "/app/web/themes/custom/ncktrnr_tw/images/dino-vegan.svg");
    }
    
    public function checkSecurity()
    {
        static $tags = [];
        static $filters = [];
        static $functions = [];

        try {
            $this->sandbox->checkSecurity(
                [],
                [],
                [],
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
