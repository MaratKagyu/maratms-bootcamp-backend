<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/22/2018
 * Time: 9:03 PM
 */

namespace App\Manager;

use App\Entity\ClientApp;
use App\Exception\HttpJsonException;
use App\Repository\ClientAppRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AccessManager
{
    /**
     * @var ClientAppRepository
     */
    private $clientAppRepo;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ClientApp
     */
    private $clientApp;

    /**
     * AccessManager constructor.
     * @param RequestStack $requestStack
     * @param ClientAppRepository $clientAppRepository
     */
    public function __construct(RequestStack $requestStack, ClientAppRepository $clientAppRepository)
    {
        $this->clientAppRepo = $clientAppRepository;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @return Request
     */
    private function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return null|string
     */
    private function getToken(): ?string
    {
        $token = $this->getRequest()->request->get('token', null);
        if (!$token) {
            $token = $this->getRequest()->query->get('token', null);
        }
        return $token;
    }

    /**
     * @return ClientApp|null
     */
    public function getClientApp(): ?ClientApp
    {
        if (!$this->clientApp) {
            $token = $this->getToken();
            if (!$token) {
                return null;
            }
            $this->clientApp = $token ? $this->clientAppRepo->findByToken($token) : null;
        }
        return $this->clientApp;
    }

    /**
     * @throws HttpJsonException
     * @return bool
     */
    public function authenticationRequired(): bool
    {
        if (!$this->getClientApp()) {
            throw new HttpJsonException([
                "status" => "error",
                "message" => "Authentication required",
                "code" => "authentication_required",
            ], 401);
        }

        return true;
    }
}