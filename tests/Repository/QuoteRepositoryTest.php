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
use App\Entity\Quote;
use App\Repository\AuthorRepository;
use App\Repository\ClientAppRepository;
use App\Repository\QuoteRepository;
use App\Tests\DataFixtures\ORM\ClientAppFixture;
use App\Tests\DataFixtures\ORM\QuoteFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\DatabasePrimer;
use Symfony\Bundle\FrameworkBundle\Client;

class QuoteRepositoryTest extends WebTestCase
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

    public function testFindByOwnerApp()
    {
        /* @var ClientAppRepository $clientAppRepo */
        $clientAppRepo = $this->entityManager->getRepository(ClientApp::class);

        /* @var QuoteRepository $quoteRepo */
        $quoteRepo = $this->entityManager->getRepository(Quote::class);

        $clientApp1 = $clientAppRepo->find(1);
        $clientApp2 = $clientAppRepo->find(2);
        $clientApp3 = $clientAppRepo->find(3);

        // 1
        $quoteListArray = array_map(
            function (Quote $quote) {
                return $quote->toExpandedDataArray();
            },
            $quoteRepo->findByOwnerApp($clientApp1)
        );

        $expectedList = [
            [
                "id" => 1,
                "ownerAppId" => 1,
                "authorId" => 1,
                "authorName" => "Stephen King",
                "text" => "Get busy living or get busy dying"
            ],
            [
                "id" => 5,
                "ownerAppId" => 1,
                "authorId" => 1,
                "authorName" => "Stephen King",
                "text" => "Talent is cheaper than table salt. What separates the talented individual from the "
                    . "successful one is a lot of hard work."
            ],
            [
                "id" => 2,
                "ownerAppId" => 1,
                "authorId" => 2,
                "authorName" => "Mark Caine",
                "text" => "The first step toward success is taken when you refuse to be a captive of the environment "
                    . "in which you first find yourself."
            ],
            [
                "id" => 3,
                "ownerAppId" => 1,
                "authorId" => 3,
                "authorName" => "Mark Twain",
                "text" => "Twenty years from now you will be more disappointed by the things that you didn’t do than "
                    . "by the ones you did do."
            ],
        ];

        $this->assertEquals(4, count($quoteListArray));
        $this->assertArraySubset($expectedList, $quoteListArray);

        // 2
        $quoteListArray = array_map(
            function (Quote $quote) {
                return $quote->toExpandedDataArray();
            },
            $quoteRepo->findByOwnerApp($clientApp2)
        );

        $expectedList = [
            [
                "id" => 4,
                "ownerAppId" => 2,
                "authorId" => 4,
                "authorName" => "Audre Lorde",
                "text" => "When I dare to be powerful – to use my strength in the service of my vision, then it "
                    . "becomes less and less important whether I am afraid."
            ],
        ];

        $this->assertEquals(1, count($quoteListArray));
        $this->assertArraySubset($expectedList, $quoteListArray);


        // 3
        $quoteListArray = array_map(
            function (Quote $quote) {
                return $quote->toExpandedDataArray();
            },
            $quoteRepo->findByOwnerApp($clientApp3)
        );


        $this->assertEquals(0, count($quoteListArray));
    }


    public function testGetRandomQuoteByOwnerApp()
    {
        /* @var ClientAppRepository $clientAppRepo */
        $clientAppRepo = $this->entityManager->getRepository(ClientApp::class);

        /* @var QuoteRepository $quoteRepo */
        $quoteRepo = $this->entityManager->getRepository(Quote::class);

        $clientApp1 = $clientAppRepo->find(1);
        $clientApp2 = $clientAppRepo->find(3);

        // 1
        $quoteData = $quoteRepo->getRandomQuoteByOwnerApp($clientApp1)->toExpandedDataArray();
        unset($quoteData['createdDateTime']);
        unset($quoteData['lastChangedDateTime']);

        $expectedList = [
            [
                "id" => 1,
                "ownerAppId" => 1,
                "authorId" => 1,
                "authorName" => "Stephen King",
                "text" => "Get busy living or get busy dying"
            ],
            [
                "id" => 5,
                "ownerAppId" => 1,
                "authorId" => 1,
                "authorName" => "Stephen King",
                "text" => "Talent is cheaper than table salt. What separates the talented individual from the "
                    . "successful one is a lot of hard work."
            ],
            [
                "id" => 2,
                "ownerAppId" => 1,
                "authorId" => 2,
                "authorName" => "Mark Caine",
                "text" => "The first step toward success is taken when you refuse to be a captive of the environment "
                    . "in which you first find yourself."
            ],
            [
                "id" => 3,
                "ownerAppId" => 1,
                "authorId" => 3,
                "authorName" => "Mark Twain",
                "text" => "Twenty years from now you will be more disappointed by the things that you didn’t do than "
                    . "by the ones you did do."
            ],
        ];

        $this->assertTrue(in_array($quoteData, $expectedList));

        // 2
        $quote = $quoteRepo->getRandomQuoteByOwnerApp($clientApp2);
        $this->assertNull($quote);
    }


    public function testFindByAuthor()
    {

        /* @var AuthorRepository $authorRepo */
        $authorRepo = $this->entityManager->getRepository(Author::class);

        /* @var QuoteRepository $quoteRepo */
        $quoteRepo = $this->entityManager->getRepository(Quote::class);

        $author1 = $authorRepo->find(1);
        $author2 = $authorRepo->find(2);
        $author3 = new Author();

        // 1
        $quoteListArray = array_map(
            function (Quote $quote) {
                return $quote->toExpandedDataArray();
            },
            $quoteRepo->findByAuthor($author1)
        );

        $expectedList = [
            [
                "id" => 1,
                "ownerAppId" => 1,
                "authorId" => 1,
                "authorName" => "Stephen King",
                "text" => "Get busy living or get busy dying"
            ],
            [
                "id" => 5,
                "ownerAppId" => 1,
                "authorId" => 1,
                "authorName" => "Stephen King",
                "text" => "Talent is cheaper than table salt. What separates the talented individual from the "
                    . "successful one is a lot of hard work."
            ],
        ];

        $this->assertEquals(2, count($quoteListArray));
        $this->assertArraySubset($expectedList, $quoteListArray);

        // 2
        $quoteListArray = array_map(
            function (Quote $quote) {
                return $quote->toExpandedDataArray();
            },
            $quoteRepo->findByAuthor($author2)
        );

        $expectedList = [
            [
                "id" => 2,
                "ownerAppId" => 1,
                "authorId" => 2,
                "authorName" => "Mark Caine",
                "text" => "The first step toward success is taken when you refuse to be a captive of the environment "
                    . "in which you first find yourself."
            ],
        ];

        $this->assertEquals(1, count($quoteListArray));
        $this->assertArraySubset($expectedList, $quoteListArray);


        // 3
        $quoteList = $quoteRepo->findByAuthor($author3);
        $this->assertEquals(0, count($quoteList));
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}