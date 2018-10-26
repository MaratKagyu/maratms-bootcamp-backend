<?php

namespace App\Repository;

use App\Entity\Author;
use App\Entity\ClientApp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Author::class);
    }

    /**
     * Finds an existing author or creates a new one
     * @param string $authorName
     * @param ClientApp $ownerApp
     * @return Author|null
     * @throws \Doctrine\ORM\ORMException
     */
    public function getByName(string $authorName, ClientApp $ownerApp): ?Author
    {
        $author = $this->findOneBy([
            "name" => $authorName,
            "ownerApp" => $ownerApp
        ]);

        if (!$author) {
            $author = new Author();
            $author
                ->setOwnerApp($ownerApp)
                ->setName($authorName);

            $this->getEntityManager()->persist($author);
        }

        return $author;
    }
}
