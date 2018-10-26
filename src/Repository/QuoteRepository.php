<?php

namespace App\Repository;

use App\Entity\Author;
use App\Entity\ClientApp;
use App\Entity\Quote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Quote|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quote|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quote[]    findAll()
 * @method Quote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuoteRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Quote::class);
    }

    /**
     * @param ClientApp $clientApp
     * @return Quote[]
     */
    public function findByOwnerApp(ClientApp $clientApp): array
    {
        return $this->getEntityManager()->createQuery(
            "
            SELECT quote, author 
            FROM \\App\\Entity\\Quote quote
            LEFT JOIN quote.author author
            WHERE quote.ownerApp = :ownerApp
            "
        )->setParameters([
            "ownerApp" =>  $clientApp
        ])->getResult();
    }

    /**
     * @param ClientApp $clientApp
     * @return Quote|null
     */
    public function getRandomQuoteByOwnerApp(ClientApp $clientApp): ?Quote
    {
        $quoteIds = $this->getEntityManager()->createQuery(
            "
            SELECT" . " quote.id FROM \\App\\Entity\\Quote quote
            WHERE quote.ownerApp = :ownerApp
            "
        )->setParameters([
            "ownerApp" =>  $clientApp
        ])->getResult();

        if (! count($quoteIds)) {
            return null;
        }

        shuffle($quoteIds);

        $randomQuoteId = array_pop($quoteIds)['id'];

        return $this->find($randomQuoteId);
    }

    /**
     * @param Author $author
     * @return Quote[]
     */
    public function findByAuthor(Author $author): array
    {
        return $this->findBy(
            ["author" => $author],
            ["text" => "ASC"]
        );
    }
}
