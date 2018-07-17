<?php

namespace ResqueBundle\Resque\Tests;

use Psr\Container\ContainerInterface;
use ResqueBundle\Resque\Tests\Implementation\ConcreteContainerAwareJob;

class ContainerAwareJobTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider setKernelOptionsDataProvider
     *
     * @param array $args
     * @param array $kernelOptions
     * @param array $expectedArgs
     */
    public function testSetKernelOptions(array $args, array $kernelOptions, array $expectedArgs)
    {
        $job = new ConcreteContainerAwareJob($args);
        $job->setKernelOptions($kernelOptions);

        $this->assertEquals($expectedArgs, $job->args);
    }

    /**
     * @return array
     */
    public function setKernelOptionsDataProvider()
    {
        return [
            'none' => [
                'args' => [],
                'kernelOptions' => [],
                'expectedArgs' => [],
            ],
            'args, no kernel options' => [
                'args' => [
                    'foo' => 'bar',
                ],
                'kernelOptions' => [],
                'expectedArgs' => [
                    'foo' => 'bar',
                ],
            ],
            'args, kernel options' => [
                'args' => [
                    'foo' => 'bar',
                ],
                'kernelOptions' => [
                    'kernel.environment' => 'prod',
                ],
                'expectedArgs' => [
                    'foo' => 'bar',
                    'kernel.environment' => 'prod',
                ],
            ],
        ];
    }

    public function testGetContainer()
    {
        $job = new ConcreteContainerAwareJob();
        $container = $job->getContainer();

        $this->assertInstanceOf(ContainerInterface::class, $container);
    }
}
