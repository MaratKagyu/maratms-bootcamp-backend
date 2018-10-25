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
     * @return array
     */
    public function findByOwnerApp(ClientApp $clientApp): array
    {
        // TODO: Optimize authors loading
        return $this->findBy(
            ["ownerApp" => $clientApp],
            ["text" => "ASC"]
        );
    }

    /**
     * @param ClientApp $clientApp
     * @return Quote
     */
    public function getRandomQuoteByOwnerApp(ClientApp $clientApp): Quote
    {
        $quoteIds = $this->getEntityManager()->createQuery(
            "
            SELECT" . " quote.id FROM \\App\\Entity\\Quote quote
            WHERE quote.ownerApp = :ownerApp
            "
        )->setParameters([
            "ownerApp" =>  $clientApp
        ])->getResult();

        shuffle($quoteIds);

        $randomQuoteId = array_pop($quoteIds)['id'];

        return $this->find($randomQuoteId);
    }

    /**
     * @param Author $author
     * @return array
     */
    public function findByAuthor(Author $author): array
    {
        return $this->findBy(
            ["author" => $author],
            ["text" => "ASC"]
        );
    }

    /*
    public function findOneBySomeField($value): ?Quote
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
