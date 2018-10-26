<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/22/2018
 * Time: 8:45 PM
 */

namespace App\Tests\DataFixtures\ORM;


use App\Entity\ClientApp;
use App\Entity\Quote;
use App\Exception\TestException;
use App\Repository\ClientAppRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Author;

class QuoteFixture extends Fixture
{
    /**
     * @param ObjectManager $manager
     * @throws TestException
     */
    public function load(ObjectManager $manager)
    {
        /* @var ClientAppRepository $clientAppRepo */
        $clientAppRepo = $manager->getRepository(ClientApp::class);

        $clientApp1 = $clientAppRepo->find(1);
        $clientApp2 = $clientAppRepo->find(2);

        if (!($clientApp1 && $clientApp2)) {
            throw new TestException("Couldn't load test client apps");
        }

        /* @var Author[] $authorList */
        $authorList = array_map(
            function ($authorData) use ($manager) {
                $author = new Author();
                $author
                    ->setName($authorData['name'])
                    ->setOwnerApp($authorData['clientApp']);

                $manager->persist($author);
                return $author;
            },
            [
                [
                    "name" => "Stephen King",
                    "clientApp" => $clientApp1,
                ],
                [
                    "name" => "Mark Caine",
                    "clientApp" => $clientApp1,
                ],
                [
                    "name" => "Mark Twain",
                    "clientApp" => $clientApp1,
                ],
                [
                    "name" => "Audre Lorde",
                    "clientApp" => $clientApp2,
                ],
            ]
        );


        $quotesDataList = [
            [// 1
                "author" => $authorList[0],
                "text" => "Get busy living or get busy dying",
            ],
            [// 2
                "author" => $authorList[1],
                "text" => "The first step toward success is taken when you refuse to be a captive of the environment "
                    . "in which you first find yourself.",
            ],
            [// 3
                "author" => $authorList[2],
                "text" => "Twenty years from now you will be more disappointed by the things that you didn’t do than "
                    . "by the ones you did do.",
            ],
            [// 4
                "author" => $authorList[3],
                "text" => "When I dare to be powerful – to use my strength in the service of my vision, then it "
                    . "becomes less and less important whether I am afraid.",
            ],
            [// 5
                "author" => $authorList[0],
                "text" => "Talent is cheaper than table salt. What separates the talented individual from the "
                    . "successful one is a lot of hard work.",
            ],
        ];

        foreach ($quotesDataList as $quoteData) {

            /* @var Author $author */
            $author = $quoteData['author'];

            $quote = new Quote();
            $quote
                ->setAuthor($author)
                ->setText($quoteData['text'])
                ->setOwnerApp($author->getOwnerApp());

            $manager->persist($quote);
        }

        $manager->flush();
    }
}