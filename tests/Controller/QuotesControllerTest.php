<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/22/2018
 * Time: 9:32 PM
 */

namespace App\Tests\Controller;


use App\Entity\Quote;
use App\Exception\TestException;
use App\Tests\DataFixtures\ORM\ClientAppFixture;
use App\Tests\DataFixtures\ORM\QuoteFixture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use App\Tests\DatabasePrimer;
use Symfony\Component\HttpFoundation\Response;

class QuotesControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @throws \Doctrine\ORM\Tools\ToolsException
     * @throws TestException
     */
    public function setUp() {
        self::bootKernel();

        DatabasePrimer::prime(self::$kernel);

        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $fixture = new ClientAppFixture();
        $fixture->load($this->entityManager);

        $fixture = new QuoteFixture();
        $fixture->load($this->entityManager);
    }

    public function testQuoteListAction()
    {
        $this->client->request("GET", "/quotes");
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $expectedResponse = [
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

        $this->assertEquals(4, count($responseData));
        $this->assertArraySubset($expectedResponse, $responseData);
    }

    public function testGetQuoteAction()
    {
        /////
        // 1. Load a quote the app has access to
        $this->client->request("GET", "/quotes/1");
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $expectedResponse = [
            "id" => 1,
            "ownerAppId" => 1,
            "authorId" => 1,
            "authorName" => "Stephen King",
            "text" => "Get busy living or get busy dying"
        ];

        $this->assertArraySubset($expectedResponse, $responseData);

        /////
        // 2. Load a quote the app has no access to
        $this->client->request("GET", "/quotes/4");
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testSaveQuoteAction()
    {
        ////
        // 1. Add a new quote
        $this->client->request("POST", "/quotes/0", ["authorName" => "My Author", "text" => "My Text"]);
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $expectedResponse = [
            "id" => 6,
            "ownerAppId" => 1,
            "authorId" => 5,
            "authorName" => "My Author",
            "text" => "My Text"
        ];

        $this->assertArraySubset($expectedResponse, $responseData);

        ////
        // 2. Update an existing quote
        $this->client->request("POST", "/quotes/6", ["authorName" => "My Author 1", "text" => "My Text 1"]);
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $expectedResponse = [
            "id" => 6,
            "ownerAppId" => 1,
            "authorId" => 6, // new author should be created
            "authorName" => "My Author 1",
            "text" => "My Text 1"
        ];

        $this->assertArraySubset($expectedResponse, $responseData);

        /////
        // 3. Update a quote the app doesn't have the access to
        $this->client->request("POST", "/quotes/4", ["authorName" => "My Author 1", "text" => "My Text 1"]);
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testDeleteQuoteAction()
    {
        ////
        // 1. Delete an existing quote
        $this->client->request("DELETE", "/quotes/3");
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $expectedResponse = [
            "status" => "ok",
            "message" => "ok",
        ];

        $this->assertArraySubset($expectedResponse, $responseData);

        $deletedQuote = $this->entityManager->getRepository(Quote::class)->find(3);
        $this->assertEquals(null, $deletedQuote);

        /////
        // 2. Delete a quote the app doesn't have the access to
        $this->client->request("POST", "/quotes/4", ["authorName" => "My Author 1", "text" => "My Text 1"]);
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }


    public function testGetQuotesByAuthorAction()
    {
        /////
        // 1. Load author quotes the app has access to
        $this->client->request("GET", "/quotes/by_author/1");
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $expectedResponse = [
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
            ]
        ];

        $this->assertArraySubset($expectedResponse, $responseData);
        $this->assertEquals(2, count($responseData));

        ////
        // 1. Load author quotes the app has NO access to
        $this->client->request("GET", "/quotes/by_author/4");
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

}