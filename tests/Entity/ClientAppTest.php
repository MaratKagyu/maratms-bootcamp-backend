<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/26/2018
 * Time: 1:28 PM
 */

namespace App\Tests\Entity;

use App\Entity\Author;
use App\Entity\ClientApp;
use PHPUnit\Framework\TestCase;

class ClientAppTest extends TestCase
{

    public function testToArray()
    {
        $clientApp = new ClientApp();

        $initialValues = [
            "id" => 0,
            "name" => 0,
            "token" => "",
            "type" => ClientApp::APP_TYPE_WORDPRESS,
            "createDateTime" => $clientApp->getCreateDateTime()->format("Y-m-d H:i:s"),
        ];

        $this->assertArraySubset($initialValues, $clientApp->toArray());

        $name = 'Name';
        $token = 'Token';
        $type = -1;

        $clientApp
            ->setId(11)
            ->setName($name)
            ->setToken($token)
            ->setType($type);

        $updatedValues = [
            "id" => 11,
            "name" => $name,
            "token" => $token,
            "type" => $type,
            "createDateTime" => $clientApp->getCreateDateTime()->format("Y-m-d H:i:s"),
        ];

        $this->assertArraySubset($updatedValues, $clientApp->toArray());
    }
}