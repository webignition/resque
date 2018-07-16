<?php

namespace ResqueBundle\Resque;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ContainerAwareJob
 * @package ResqueBundle\Resque
 */
abstract class ContainerAwareJob extends Job
{
    const ENV_KERNEL_CLASS = 'KERNEL_CLASS';
    const ENV_USE_DOT_ENV = 'USE_DOT_ENV';

    /**
     * @var KernelInterface
     */
    private $kernel = NULL;

    /**
     * @param array $kernelOptions
     */
    public function setKernelOptions(array $kernelOptions)
    {
        $this->args = \array_merge($this->args, $kernelOptions);
    }

    /**
     *
     */
    public function tearDown()
    {
        if ($this->kernel) {
            $this->kernel->shutdown();
        }
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        if ($this->kernel === NULL) {
            $this->kernel = $this->createKernel();
            $this->kernel->boot();
        }

        return $this->kernel->getContainer();
    }

    /**
     * @return KernelInterface
     */
    protected function createKernel()
    {
        if (array_key_exists(self::ENV_KERNEL_CLASS, $_SERVER)) {
            $class = $_SERVER[self::ENV_KERNEL_CLASS];
        } else {
            $finder = new Finder();
            $finder->name('*Kernel.php')->depth(0)->in($this->args['kernel.root_dir']);
            $results = iterator_to_array($finder);
            $file = current($results);
            $class = $file->getBasename('.php');

            require_once $file;
        }

        if (array_key_exists(self::ENV_USE_DOT_ENV, $_SERVER) && $_SERVER[self::ENV_USE_DOT_ENV]) {
            (new Dotenv())->load(__DIR__.'/../../../.env');
        }

        return new $class(
            isset($this->args['kernel.environment']) ? $this->args['kernel.environment'] : 'dev',
            isset($this->args['kernel.debug']) ? $this->args['kernel.debug'] : TRUE
        );
    }
}
