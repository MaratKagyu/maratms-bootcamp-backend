<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 2/17/2018
 * Time: 7:44 AM
 */

namespace App\Controller;

use App\Entity\ClientApp;
use App\Repository\ClientAppRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class ClientAppController extends Controller
{
    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ClientAppRepository $clientAppRepository
     * @Route(
     *     "/register",
     *     name="registerAppAction",
     *     methods={"POST"}
     * )
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function registerAppAction(
        Request $request,
        EntityManagerInterface $entityManager,
        ClientAppRepository $clientAppRepository
    )
    {
        $token = $request->request->get('token', '');
        if ((!$token) || (strlen($token) > 255)) {
            return $this->json([
                "status" => "error",
                "message" => "Invalid app token",
                "code" => "invalid_app_token",
            ], 400);
        }

        $clientApp = $clientAppRepository->findByToken($token);
        if ($clientApp) {
            return $this->json([
                "status" => "ok",
                "message" => "The app is already registered",
            ]);
        } else {
            $clientApp = new ClientApp();
            $clientApp
                ->setName('')
                ->setToken($token)
                ->setType(ClientApp::APP_TYPE_WORDPRESS);

            $entityManager->persist($clientApp);
            $entityManager->flush();

            return $this->json([
                "status" => "ok",
                "message" => "The app is registered",
            ]);
        }
    }

}