<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/26/2018
 * Time: 12:40 PM
 */

namespace App\Tests\Repository;

use App\Entity\Author;
use App\Entity\ClientApp;
use App\Repository\AuthorRepository;
use App\Tests\DataFixtures\ORM\ClientAppFixture;
use App\Tests\DataFixtures\ORM\QuoteFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\DatabasePrimer;
use Symfony\Bundle\FrameworkBundle\Client;

class AuthorRepositoryTest extends WebTestCase
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

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function testGetByName()
    {
        /* @var ClientApp $clientApp1 */
        $clientApp1 = $this->entityManager->getRepository(ClientApp::class)->find(1);

        /* @var ClientApp $clientApp2 */
        $clientApp2 = $this->entityManager->getRepository(ClientApp::class)->find(2);

        /* @var AuthorRepository $authorRepo */
        $authorRepo = $this->entityManager->getRepository(Author::class);

        $existingAuthorName1 = "Stephen King";
        $existingAuthorName2 = "Audre Lorde";
        $nonexistentAuthorName3 = "New Author";

        $author = $authorRepo->getByName($existingAuthorName1, $clientApp1);
        $this->assertEquals(1, $author->getId());

        // This should create a new author (because one isn't associated with client app 2)
        $author = $authorRepo->getByName($existingAuthorName1, $clientApp2);
        $this->assertEquals(0, $author->getId());

        $author = $authorRepo->getByName($existingAuthorName2, $clientApp2);
        $this->assertEquals(4, $author->getId());

        $author = $authorRepo->getByName($nonexistentAuthorName3, $clientApp1);
        $this->assertEquals(0, $author->getId());
    }


    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}