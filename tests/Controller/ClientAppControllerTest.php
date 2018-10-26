<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/22/2018
 * Time: 9:32 PM
 */

namespace App\Tests\Controller;


use App\Exception\TestException;
use App\Tests\DataFixtures\ORM\ClientAppFixture;
use App\Tests\DataFixtures\ORM\QuoteFixture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use App\Tests\DatabasePrimer;
use Symfony\Component\HttpFoundation\Response;

class ClientAppControllerTest extends WebTestCase
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
    public function setUp()
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

    public function testRegisterAppAction()
    {
        ////
        // 1. First try with token 1
        $token1 = 'example token1';
        $token2 = 'example token2';

        $this->client->request("POST", "/register", ["token" => $token1]);
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(
            [
                "status" => "ok",
                "message" => "The app is registered",
            ],
            $responseData
        );

        ////
        // 2. Second try with token 1
        $this->client->request("POST", "/register", ["token" => $token1]);
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(
            [
                "status" => "ok",
                "message" => "The app is already registered",
            ],
            $responseData
        );

        ////
        // 3. Last try with token 2
        $this->client->request("POST", "/register", ["token" => $token2]);
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(
            [
                "status" => "ok",
                "message" => "The app is registered",
            ],
            $responseData
        );
    }


}