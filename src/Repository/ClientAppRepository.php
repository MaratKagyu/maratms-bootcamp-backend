<?php

namespace App\Repository;

use App\Entity\ClientApp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ClientApp|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClientApp|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClientApp[]    findAll()
 * @method ClientApp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientAppRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ClientApp::class);
    }

}
