<?php

namespace ResqueBundle\Resque\Tests\Implementation;

use ResqueBundle\Resque\ContainerAwareJob;

class ConcreteContainerAwareJob extends ContainerAwareJob
{
    /**
     * {@inheritdoc}
     */
    public function run($args)
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getContainer()
    {
        return parent::getContainer();
    }
}
