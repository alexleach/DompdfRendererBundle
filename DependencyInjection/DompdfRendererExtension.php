<?php

namespace KimaiPlugin\DompdfRendererBundle\DependencyInjection;

use App\Plugin\AbstractPluginExtension;
use App\Pdf\HtmlToPdfConverter;

use KimaiPlugin\DompdfRendererBundle\Pdf\DompdfConverter;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Parser;

class DompdfRendererExtension extends AbstractPluginExtension implements PrependExtensionInterface, CompilerPassInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        // Load the current configuration from file
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $this->registerBundleConfiguration($container, $config);

        // Is 'renderer' set to 'dompdf' in the configuration file?
        // Or KIMAI_PDF_RENDERER environment variable?
        //
        // N.B. Apparently need to reload the cache every time we want to
        // switch the pdf renderer...
        $renderer = getenv("KIMAI_PDF_RENDERER");
        if ($renderer == false) {
            $renderer = $config['renderer'];
        }

        if ($renderer == 'dompdf') {
            $loader = new Loader\YamlFileLoader(
                $container, 
                new FileLocator(__DIR__ . '/../Resources/config')
            );
            // Load the service definition from services.yaml
            $loader->load('services.yaml');
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        // load the bundle's configuration file
        $dompdfConfig = file_get_contents(__DIR__ . '/../Resources/config/dompdf_config.yaml');
        if ($dompdfConfig === false) {
            throw new \Exception('Could not read Resources/config/dompdf_config.yaml');
        }

        $yamlParser = new Parser();
        $config = $yamlParser->parse($dompdfConfig);

        // Set '{"renderer": (mpdf|dompdf)}' on the dompdf_renderer namespace
        $container->prependExtensionConfig('dompdf_renderer', $config['dompdf_renderer']);

        // This adds our included templates.
        $container->prependExtensionConfig('kimai', [
            'invoice' => [
                'documents' => [
                    'var/plugins/DompdfRendererBundle/Resources/invoices/',
                ]
            ]
        ]);
    }

    /**
     *
     * `process` is called in the Compile stage; the only stage where the
     * existing HtmlToPdfConverter alias can apparently be overridden.
     *
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(DompdfConverter::class)) {
            // Then we haven't selected to use Dompdf, so leave the existing
            // alias in place (and use MPdf instead)
            return;
        }
        $this->overrideDefinition($container);
    }

    private function overrideDefinition(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(HtmlToPdfConverter::class)) {
            // If it exists on the container as a definition
            $oldDefinition = $container->getDefinition(HtmlToPdfConverter::class);
        }
        else if ($container->has(HtmlToPdfConverter::class)) {
            // If get here, then it's an alias, but we retrieve it as a Definition
            $oldDefinition = $container->findDefinition(HtmlToPdfConverter::class);
            // Remove the alias? Doesn't seem necessary, but why not?
            $container->removeAlias(HtmlToPdfConverter::class);
        }
        else {
            // Not present?
            return;
        }
        // Remove the old definition? Doesn't seem strictly necessary, can just
        // set a new one?
        $container->removeDefinition(HtmlToPdfConverter::class);

        $alias = new Alias(DompdfConverter::class);
        $container->setAlias(HtmlToPdfConverter::class, $alias);
    }
}
