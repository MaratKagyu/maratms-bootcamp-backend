<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/26/2018
 * Time: 11:04 AM
 */

namespace App\Tests\Manager;

use App\Entity\ClientApp;
use App\Exception\HttpJsonException;
use App\Manager\AccessManager;
use App\Repository\ClientAppRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class AccessManagerTest extends TestCase
{
    const APP_TOKEN = 1111;
    const APP_NAME = 'App name';

    /**
     * @return RequestStack
     */
    private function getMockedAuthorizedRequestStack(): RequestStack
    {
        // Mock request object
        $request = \Mockery::mock(Request::class);
        $request->request = new ParameterBag(["token" => self::APP_TOKEN]);
        $request->query = new ParameterBag([]);

        // Mock request stack object
        return \Mockery::mock(RequestStack::class)
            ->shouldReceive('getCurrentRequest')
            ->andReturn($request)
            ->getMock()
        ;
    }

    /**
     * @return RequestStack
     */
    private function getMockedUnauthorizedRequestStack(): RequestStack
    {
        // Mock request object
        $request = \Mockery::mock(Request::class);
        $request->request = new ParameterBag([]);
        $request->query = new ParameterBag([]);

        // Mock request stack object
        return \Mockery::mock(RequestStack::class)
            ->shouldReceive('getCurrentRequest')
            ->andReturn($request)
            ->getMock()
            ;
    }

    /**
     * @return ClientAppRepository
     */
    private function getMockedClientRepo(): ClientAppRepository
    {
        $clientApp = new ClientApp();
        $clientApp
            ->setId(1)
            ->setName(self::APP_NAME)
            ->setToken(self::APP_TOKEN)
            ->setType(ClientApp::APP_TYPE_WORDPRESS)
        ;

        return \Mockery::mock(ClientAppRepository::class)
            ->shouldReceive('findByToken')
            ->andReturn($clientApp)
            ->getMock()
        ;
    }

    /**
     * @return ClientAppRepository
     */
    private function getMockedEmptyClientRepo(): ClientAppRepository
    {
        return \Mockery::mock(ClientAppRepository::class)
            ->shouldReceive('findByToken')
            ->andReturnNull()
            ->getMock()
            ;
    }

    /**
     * @throws \App\Exception\HttpJsonException
     */
    public function testGetExistingClientApp()
    {
        $mockedStack = $this->getMockedAuthorizedRequestStack();
        $mockedClientRepo = $this->getMockedClientRepo();

        $accessManager = new AccessManager($mockedStack, $mockedClientRepo);

        // Test if Client App is delivered
        $this->assertArraySubset(
            [
                "id" => 1,
                "name" => self::APP_NAME,
                "token" => self::APP_TOKEN,
                "type" => ClientApp::APP_TYPE_WORDPRESS,
            ],
            $accessManager->getClientApp()->toArray()
        );

        // Test is Authentication requirement doesn't throw an error
        $this->assertTrue($accessManager->authenticationRequired());
    }

    /**
     * @throws \App\Exception\HttpJsonException
     */
    public function testGetNonexistentClientApp()
    {
        $mockedStack = $this->getMockedAuthorizedRequestStack();
        $mockedClientRepo = $this->getMockedEmptyClientRepo();

        $accessManager = new AccessManager($mockedStack, $mockedClientRepo);

        $this->assertNull($accessManager->getClientApp());

        $this->expectException(HttpJsonException::class);

        // Test is Authentication requirement doesn't throw an error
        $this->assertTrue($accessManager->authenticationRequired());
    }

    /**
     * @throws \App\Exception\HttpJsonException
     */
    public function testEmptyToken()
    {
        $mockedStack = $this->getMockedUnauthorizedRequestStack();
        $mockedClientRepo = $this->getMockedClientRepo();

        $accessManager = new AccessManager($mockedStack, $mockedClientRepo);

        $this->assertNull($accessManager->getClientApp());

        $this->expectException(HttpJsonException::class);

        // Test is Authentication requirement doesn't throw an error
        $this->assertTrue($accessManager->authenticationRequired());
    }
}



