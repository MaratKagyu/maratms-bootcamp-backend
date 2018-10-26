<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/22/2018
 * Time: 8:45 PM
 */

namespace App\Tests\DataFixtures\ORM;


use App\Entity\ClientApp;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ClientAppFixture extends Fixture
{
    const TEST_TOKEN_1 = "test token 1";
    const TEST_TOKEN_2 = "test token 2";
    const TEST_TOKEN_3 = "test token 3";

    public function load(ObjectManager $manager)
    {
        $clientAppDataList = [
            [
                "name" => "Primary Test Client App",
                "type" => ClientApp::APP_TYPE_WORDPRESS,
                "token" => self::TEST_TOKEN_1
            ],
            [
                "name" => "Secondary Test Client App",
                "type" => ClientApp::APP_TYPE_WORDPRESS,
                "token" => self::TEST_TOKEN_2
            ],
            [
                "name" => "Test Client App with no quotes",
                "type" => ClientApp::APP_TYPE_WORDPRESS,
                "token" => self::TEST_TOKEN_3
            ],
        ];

        foreach ($clientAppDataList as $clientAppData) {
            $clientApp = new ClientApp();
            $clientApp
                ->setName($clientAppData['name'])
                ->setType($clientAppData['type'])
                ->setToken($clientAppData['token']);

            $manager->persist($clientApp);
        }

        $manager->flush();
    }
}