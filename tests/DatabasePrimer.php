<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\HttpKernel\KernelInterface;

class DatabasePrimer
{
    /**
     * @param KernelInterface $kernel
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public static function prime(KernelInterface $kernel)
    {
        // Make sure we are in the test environment
        if ('test' !== $kernel->getEnvironment()) {
            throw new LogicException('Primer must be executed in the test environment');
        }

        // Get the entity manager from the service container
        /* @var EntityManagerInterface $entityManager */
        $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        // Run the schema update tool using our entity metadata
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }
}
