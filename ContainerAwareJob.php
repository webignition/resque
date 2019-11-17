<?php

namespace ResqueBundle\Resque;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ContainerAwareJob
 * @package ResqueBundle\Resque
 */
abstract class ContainerAwareJob
{
    const ENV_KERNEL_CLASS = 'KERNEL_CLASS';
    const ENV_DOT_ENV_PATH = 'DOTENV_PATH';

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
        if (!isset($_SERVER['APP_ENV'])) {
            if (!class_exists(Dotenv::class)) {
                throw new \RuntimeException(
                    'APP_ENV environment variable is not defined. You need to define environment variables '
                    .'for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a '
                    .'.env file.'
                );
            }

            if (!isset($_SERVER[self::ENV_DOT_ENV_PATH])) {
                throw $this->createEnvironmentVariableNotSetException(self::ENV_DOT_ENV_PATH);
            }

            $dotEnvPath = $_SERVER[self::ENV_DOT_ENV_PATH];
            if (substr($dotEnvPath, 0, 1) !== '/') {
                $dotEnvPath = __DIR__ . '/' . $dotEnvPath;
            }

            (new Dotenv())->load($dotEnvPath);
        }

        if (!isset($_SERVER[self::ENV_KERNEL_CLASS])) {
            throw $this->createEnvironmentVariableNotSetException(self::ENV_KERNEL_CLASS);
        }

        $class = $_SERVER[self::ENV_KERNEL_CLASS];

        return new $class(
            isset($this->args['kernel.environment']) ? $this->args['kernel.environment'] : 'dev',
            isset($this->args['kernel.debug']) ? $this->args['kernel.debug'] : TRUE
        );
    }

    /**
     * @param string $name
     *
     * @return \RuntimeException
     */
    private function createEnvironmentVariableNotSetException($name)
    {
        return new \RuntimeException(sprintf('%s environment variable not set', $name));
    }
}
