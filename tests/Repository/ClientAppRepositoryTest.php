<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/26/2018
 * Time: 12:40 PM
 */

namespace App\Tests\Repository;

use App\Entity\ClientApp;
use App\Repository\ClientAppRepository;
use App\Tests\DataFixtures\ORM\ClientAppFixture;
use App\Tests\DataFixtures\ORM\QuoteFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\DatabasePrimer;
use Symfony\Bundle\FrameworkBundle\Client;

class ClientAppRepositoryTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var Client
     */
    private $client;

    /**
     * @throws \App\Exception\TestException
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    protected function setUp()
    {
        self::bootKernel();

        DatabasePrimer::prime(self::$kernel);

        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $fixture = new ClientAppFixture();
        $fixture->load($this->entityManager);

        $fixture = new QuoteFixture();
        $fixture->load($this->entityManager);

    }


    public function testFindByToken()
    {
        /* @var ClientAppRepository $clientAppRepo */
        $clientAppRepo = $this->entityManager->getRepository(ClientApp::class);

        $existingToken1 = ClientAppFixture::TEST_TOKEN_1;
        $existingToken2 = ClientAppFixture::TEST_TOKEN_2;
        $nonexistentToken3 = 'invalid token';

        $client1 = $clientAppRepo->findByToken($existingToken1);
        $this->assertEquals(1, $client1->getId());

        $client2 = $clientAppRepo->findByToken($existingToken2);
        $this->assertEquals(2, $client2->getId());

        $client3 = $clientAppRepo->findByToken($nonexistentToken3);
        $this->assertNull($client3);
    }


    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}