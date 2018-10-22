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

        if (! ($clientApp1 && $clientApp2)) {
            throw new TestException("Couldn't load test client apps");
        }

        $quotesDataList = [
            [// 1
                "author" => "Stephen King",
                "text" => "Get busy living or get busy dying",
                "clientApp" => $clientApp1,
            ],
            [// 2
                "author" => "Mark Caine",
                "text" => "The first step toward success is taken when you refuse to be a captive of the environment "
                    . "in which you first find yourself.",
                "clientApp" => $clientApp1,
            ],
            [// 3
                "author" => "Mark Twain",
                "text" => "Twenty years from now you will be more disappointed by the things that you didn’t do than "
                    . "by the ones you did do.",
                "clientApp" => $clientApp1,
            ],
            [// 4
                "author" => "Audre Lorde",
                "text" => "When I dare to be powerful – to use my strength in the service of my vision, then it "
                    . "becomes less and less important whether I am afraid.",
                "clientApp" => $clientApp2,
            ],
        ];


        foreach ($quotesDataList as $quoteData) {
            $author = new Author();
            $author
                ->setOwnerApp($quoteData['clientApp'])
                ->setName($quoteData['author'])
            ;

            $manager->persist($author);

            $quote = new Quote();
            $quote
                ->setAuthor($author)
                ->setText($quoteData['text'])
                ->setOwnerApp($quoteData['clientApp'])
            ;


            $manager->persist($quote);
        }

        $manager->flush();
    }
}