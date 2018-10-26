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
use App\Entity\Quote;
use PHPUnit\Framework\TestCase;

class QuoteTest extends TestCase
{

    /**
     * @var ClientApp
     */
    private $clientApp1;

    /**
     * @var Author
     */
    private $author1;


    public function setUp()
    {
        $clientApp1 = new ClientApp();
        $clientApp1
            ->setId(3)
            ->setName("App name")
            ->setToken("App token")
            ->setType(ClientApp::APP_TYPE_WORDPRESS);
        $this->clientApp1 = $clientApp1;


        $this->author1 = new Author();
        $this->author1
            ->setOwnerApp($this->clientApp1)
            ->setId(5);
    }

    public function testToArray()
    {
        $quote = new Quote();

        $initialValues = [
            "id" => 0,
            "ownerAppId" => 0,
            "authorId" => 0,
            "text" => "",
            "createdDateTime" => $quote->getCreatedDateTime()->format("Y-m-d H:i:s"),
            "lastChangedDateTime" => $quote->getLastChangedDateTime()->format("Y-m-d H:i:s"),
        ];

        $this->assertArraySubset($initialValues, $quote->toArray());

        $quoteText = 'Quote text';

        $quote
            ->setId(11)
            ->setAuthor($this->author1)
            ->setOwnerApp($this->clientApp1)
            ->setText($quoteText);

        $updatedValues = [
            "id" => 11,
            "ownerAppId" => $this->clientApp1->getId(),
            "authorId" => $this->author1->getId(),
            "text" => $quoteText,
            "createdDateTime" => $quote->getCreatedDateTime()->format("Y-m-d H:i:s"),
            "lastChangedDateTime" => $quote->getLastChangedDateTime()->format("Y-m-d H:i:s"),
        ];


        $this->assertArraySubset($updatedValues, $quote->toArray());
    }


    public function testToExpandedDataArray()
    {
        $quote = new Quote();

        $initialValues = [
            "id" => 0,
            "ownerAppId" => 0,
            "authorId" => 0,
            "authorName" => "",
            "text" => "",
            "createdDateTime" => $quote->getCreatedDateTime()->format("Y-m-d H:i:s"),
            "lastChangedDateTime" => $quote->getLastChangedDateTime()->format("Y-m-d H:i:s"),
        ];

        $this->assertArraySubset($initialValues, $quote->toExpandedDataArray());

        $quoteText = 'Quote text';

        $quote
            ->setId(11)
            ->setAuthor($this->author1)
            ->setOwnerApp($this->clientApp1)
            ->setText($quoteText);

        $updatedValues = [
            "id" => 11,
            "ownerAppId" => $this->clientApp1->getId(),
            "authorId" => $this->author1->getId(),
            "authorName" => $this->author1->getName(),
            "text" => $quoteText,
            "createdDateTime" => $quote->getCreatedDateTime()->format("Y-m-d H:i:s"),
            "lastChangedDateTime" => $quote->getLastChangedDateTime()->format("Y-m-d H:i:s"),
        ];

        $this->assertArraySubset($updatedValues, $quote->toExpandedDataArray());
    }

}