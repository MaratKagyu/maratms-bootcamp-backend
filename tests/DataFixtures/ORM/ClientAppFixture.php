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
    public function load(ObjectManager $manager)
    {
        $clientAppDataList = [
            [
                "name" => "Primary Test Client App",
                "type" => ClientApp::APP_TYPE_WORDPRESS,
            ],
            [
                "name" => "Secondary Test Client App",
                "type" => ClientApp::APP_TYPE_WORDPRESS,
            ],
        ];

        foreach ($clientAppDataList as $clientAppData) {
            $clientApp = new ClientApp();
            $clientApp
                ->setName($clientAppData['name'])
                ->setType($clientAppData['type'])
            ;

            $manager->persist($clientApp);
        }


        $manager->flush();
    }
}