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

class AuthorTest extends TestCase
{

    /**
     * @var ClientApp
     */
    private $clientApp1;

    public function setUp()
    {
        $clientApp1 = new ClientApp();
        $clientApp1
            ->setId(3)
            ->setName("App name")
            ->setToken("App token")
            ->setType(ClientApp::APP_TYPE_WORDPRESS);
        $this->clientApp1 = $clientApp1;

    }

    public function testToArray()
    {
        $author = new Author();

        $initialValues = [
            "id" => 0,
            "ownerAppId" => 0,
            "name" => "",
            "createdDateTime" => $author->getCreatedDateTime()->format("Y-m-d H:i:s"),
            "lastChangedDateTime" => $author->getLastChangedDateTime()->format("Y-m-d H:i:s"),
        ];

        $this->assertArraySubset($initialValues, $author->toArray());

        $authorName = 'Author name';

        $author
            ->setId(11)
            ->setOwnerApp($this->clientApp1)
            ->setName($authorName);

        $updatedValues = [
            "id" => 11,
            "ownerAppId" => 3,
            "name" => $authorName,
            "createdDateTime" => $author->getCreatedDateTime()->format("Y-m-d H:i:s"),
            "lastChangedDateTime" => $author->getLastChangedDateTime()->format("Y-m-d H:i:s"),
        ];

        $this->assertArraySubset($updatedValues, $author->toArray());
    }
}