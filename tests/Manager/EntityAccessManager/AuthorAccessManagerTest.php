<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/26/2018
 * Time: 11:46 AM
 */

namespace App\Tests\Manager\EntityAccessManager;

use App\Entity\Author;
use App\Manager\EntityAccessManager\AuthorAccessManager;
use PHPUnit\Framework\TestCase;
use App\Entity\ClientApp;
use App\Exception\HttpJsonException;
use App\Repository\ClientAppRepository;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class AuthorAccessManagerTest extends TestCase
{
    /**
     * @var ClientApp
     */
    private $clientApp1;

    /**
     * @var ClientApp
     */
    private $clientApp2;

    /**
     * @var Author
     */
    private $author1;

    /**
     * @var Author
     */
    private $author2;


    public function setUp()
    {
        $clientApp1 = new ClientApp();
        $clientApp1
            ->setId(1)
            ->setName("App name")
            ->setToken("App token")
            ->setType(ClientApp::APP_TYPE_WORDPRESS);
        $this->clientApp1 = $clientApp1;

        $clientApp2 = new ClientApp();
        $clientApp2
            ->setId(2)
            ->setName("App name2")
            ->setToken("App token2")
            ->setType(ClientApp::APP_TYPE_WORDPRESS);
        $this->clientApp2 = $clientApp2;

        $this->author1 = new Author();
        $this->author1->setOwnerApp($this->clientApp1);

        $this->author2 = new Author();
        $this->author2->setOwnerApp($this->clientApp2);
    }


    /**
     * @return RequestStack
     */
    private function getMockedAuthorizedRStack(): RequestStack
    {
        // Mock request object
        $request = \Mockery::mock(Request::class);
        $request->request = new ParameterBag(["token" => "whatever"]);
        $request->query = new ParameterBag([]);

        // Mock request stack object
        return \Mockery::mock(RequestStack::class)
            ->shouldReceive('getCurrentRequest')
            ->andReturn($request)
            ->getMock();
    }

    /**
     * @return RequestStack
     */
    private function getMockedUnauthorizedRStack(): RequestStack
    {
        // Mock request object
        $request = \Mockery::mock(Request::class);
        $request->request = new ParameterBag([]);
        $request->query = new ParameterBag([]);

        // Mock request stack object
        return \Mockery::mock(RequestStack::class)
            ->shouldReceive('getCurrentRequest')
            ->andReturn($request)
            ->getMock();
    }

    /**
     * @return ClientAppRepository
     */
    private function getMockedClientRepo(): ClientAppRepository
    {
        return \Mockery::mock(ClientAppRepository::class)
            ->shouldReceive('findByToken')
            ->andReturn($this->clientApp1)
            ->getMock();
    }

    public function testClientAppIsAssociatedWithAuthor()
    {
        // If authenticated
        $accessManager = new AuthorAccessManager($this->getMockedAuthorizedRStack(), $this->getMockedClientRepo());
        $this->assertTrue($accessManager->clientAppIsAssociatedWithAuthor($this->author1));
        $this->assertFalse($accessManager->clientAppIsAssociatedWithAuthor($this->author2));

        // If not authenticated
        $accessManager = new AuthorAccessManager($this->getMockedUnauthorizedRStack(), $this->getMockedClientRepo());
        $this->assertFalse($accessManager->clientAppIsAssociatedWithAuthor($this->author1));
        $this->assertFalse($accessManager->clientAppIsAssociatedWithAuthor($this->author2));
    }


    public function testIsReadable()
    {
        // If authenticated
        $accessManager = new AuthorAccessManager($this->getMockedAuthorizedRStack(), $this->getMockedClientRepo());
        $this->assertTrue($accessManager->isReadable($this->author1));
        $this->assertFalse($accessManager->isReadable($this->author2));

        // If not authenticated
        $accessManager = new AuthorAccessManager($this->getMockedUnauthorizedRStack(), $this->getMockedClientRepo());
        $this->assertFalse($accessManager->isReadable($this->author1));
        $this->assertFalse($accessManager->isReadable($this->author2));
    }

    public function testIsWritable()
    {
        // If authenticated
        $accessManager = new AuthorAccessManager($this->getMockedAuthorizedRStack(), $this->getMockedClientRepo());
        $this->assertTrue($accessManager->isWritable($this->author1));
        $this->assertFalse($accessManager->isWritable($this->author2));

        // If not authenticated
        $accessManager = new AuthorAccessManager($this->getMockedUnauthorizedRStack(), $this->getMockedClientRepo());
        $this->assertFalse($accessManager->isWritable($this->author1));
        $this->assertFalse($accessManager->isWritable($this->author2));
    }

    /**
     * @throws HttpJsonException
     */
    public function testReadAccessRequired()
    {
        // If authenticated
        $accessManager = new AuthorAccessManager($this->getMockedAuthorizedRStack(), $this->getMockedClientRepo());
        $this->assertTrue($accessManager->readAccessRequired($this->author1));

        $exceptionThrown = null;
        try {
            $accessManager->readAccessRequired($this->author2);
        } catch (HttpJsonException $exception) {
            $exceptionThrown = $exception;
        }
        $this->assertNotNull($exceptionThrown);

        // If not authenticated
        $accessManager = new AuthorAccessManager($this->getMockedUnauthorizedRStack(), $this->getMockedClientRepo());
        $exceptionThrown = null;
        try {
            $accessManager->readAccessRequired($this->author1);
        } catch (HttpJsonException $exception) {
            $exceptionThrown = $exception;
        }
        $this->assertNotNull($exceptionThrown);

        $exceptionThrown = null;
        try {
            $accessManager->readAccessRequired($this->author2);
        } catch (HttpJsonException $exception) {
            $exceptionThrown = $exception;
        }
        $this->assertNotNull($exceptionThrown);
    }

    /**
     * @throws HttpJsonException
     */
    public function testWriteAccessRequired()
    {
        // If authenticated
        $accessManager = new AuthorAccessManager($this->getMockedAuthorizedRStack(), $this->getMockedClientRepo());
        $this->assertTrue($accessManager->writeAccessRequired($this->author1));

        $exceptionThrown = null;
        try {
            $accessManager->writeAccessRequired($this->author2);
        } catch (HttpJsonException $exception) {
            $exceptionThrown = $exception;
        }
        $this->assertNotNull($exceptionThrown);

        // If not authenticated
        $accessManager = new AuthorAccessManager($this->getMockedUnauthorizedRStack(), $this->getMockedClientRepo());
        $exceptionThrown = null;
        try {
            $accessManager->writeAccessRequired($this->author1);
        } catch (HttpJsonException $exception) {
            $exceptionThrown = $exception;
        }
        $this->assertNotNull($exceptionThrown);

        $exceptionThrown = null;
        try {
            $accessManager->writeAccessRequired($this->author2);
        } catch (HttpJsonException $exception) {
            $exceptionThrown = $exception;
        }
        $this->assertNotNull($exceptionThrown);
    }
}