<?php

declare(strict_types=1);

namespace Drupal\Tests\drupal_cms_helper\Kernel;

use Drupal\drupal_cms_helper\ComposerRunner;
use Drupal\KernelTests\KernelTestBase;
use PhpTuf\ComposerStager\API\Process\Service\ComposerProcessRunnerInterface;
use PHPUnit\Framework\Attributes\Group;

#[Group('drupal_cms_helper')]
final class ServiceOverridesTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'drupal_cms_helper',
    'package_manager',
    'path_alias',
    'update',
  ];

  /**
   * Tests that container services are altered correctly.
   */
  public function testServiceAlterations(): void {
    $this->assertInstanceOf(
      ComposerRunner::class,
      $this->container->get(ComposerProcessRunnerInterface::class),
    );
  }

}
