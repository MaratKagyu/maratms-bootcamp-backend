<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/22/2018
 * Time: 9:03 PM
 */

namespace App\Manager;


use App\Entity\ClientApp;
use App\Repository\ClientAppRepository;

class AccessManager
{
    /**
     * @var ClientAppRepository
     */
    private $clientAppRepo;

    /**
     * AccessManager constructor.
     * @param ClientAppRepository $clientAppRepository
     */
    public function __construct(ClientAppRepository $clientAppRepository)
    {
        $this->clientAppRepo = $clientAppRepository;
    }

    /**
     * @return ClientApp|null
     */
    public function getClientApp(): ?ClientApp
    {
        return $this->clientAppRepo->find(1);
    }
}