<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 2/17/2018
 * Time: 7:44 AM
 */

namespace App\Controller;

use App\Entity\Quote;
use App\Exception\HttpJsonException;
use App\Manager\EntityAccessManager\AuthorAccessManager;
use App\Manager\EntityAccessManager\QuoteAccessManager;
use App\Repository\AuthorRepository;
use App\Repository\QuoteRepository;
use App\Service\Validator;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class QuotesController extends Controller
{
    /**
     * @param QuoteAccessManager $accessManager
     * @param QuoteRepository $quoteRepository
     * @Route(
     *     "/quotes",
     *     name="getQuoteListAction",
     *     methods={"GET"}
     * )
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws HttpJsonException
     */
    public function getQuoteListAction(QuoteAccessManager $accessManager, QuoteRepository $quoteRepository)
    {
        return $this->json(array_map(
            function (Quote $quote) {
                return $quote->toExpandedDataArray();
            },
            $quoteRepository->findByOwnerApp($accessManager->getClientApp())
        ));
    }

    /**
     * @param QuoteAccessManager $accessManager
     * @param QuoteRepository $quoteRepository
     * @Route(
     *     "/quotes/random",
     *     name="getRandomQuoteAction",
     *     methods={"GET"}
     * )
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws HttpJsonException
     */
    public function getRandomQuoteAction(QuoteAccessManager $accessManager, QuoteRepository $quoteRepository)
    {
        $quote = $quoteRepository->getRandomQuoteByOwnerApp($accessManager->getClientApp());
        if (! $quote) {
            throw new HttpJsonException([
                "status" => "error",
                "message" => "No quotes found",
                "code" => "quotes_not_found",
            ], 404);
        }

        return $this->json($quote->toExpandedDataArray());

    }

    /**
     * @param int $authorId
     * @param AuthorAccessManager $accessManager
     * @param QuoteRepository $quoteRepository
     * @param AuthorRepository $authorRepository
     * @Route(
     *     "/quotes/by_author/{authorId}",
     *     name="getQuotesByAuthorAction",
     *     requirements={"quoteId"="\d+"},
     *     methods={"GET"}
     * )
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws HttpJsonException
     */
    public function getQuotesByAuthorAction(
        int $authorId,
        AuthorAccessManager $accessManager,
        QuoteRepository $quoteRepository,
        AuthorRepository $authorRepository
    ) {
        $author = $authorRepository->find($authorId);
        if (! $author) {
            throw new HttpJsonException([
                "status" => "error",
                "message" => "Author not found",
                "code" => "author_not_found",
                "authorId" => $authorId,
            ], 404);
        }

        $accessManager->readAccessRequired($author);


        return $this->json(array_map(
            function (Quote $quote) {
                return $quote->toExpandedDataArray();
            },
            $quoteRepository->findByAuthor($author)
        ));
    }

    /**
     * @param int $quoteId
     * @param QuoteAccessManager $accessManager
     * @param QuoteRepository $quoteRepository
     * @Route(
     *     "/quotes/{quoteId}",
     *     name="getQuoteAction",
     *     requirements={"quoteId"="\d+"},
     *     methods={"GET"}
     * )
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws HttpJsonException
     */
    public function getQuoteAction(
        int $quoteId,
        QuoteAccessManager $accessManager,
        QuoteRepository $quoteRepository
    ) {
        $quote = $quoteRepository->find($quoteId);
        if (! $quote) {
            throw new HttpJsonException([
                "status" => "error",
                "message" => "Quote not found",
                "code" => "quote_not_found",
                "quoteId" => $quoteId,
            ], 404);

        } else {
            // Throws a forbidden error, in the app doesn't have the access
            $accessManager->readAccessRequired($quote);

            return $this->json($quote->toExpandedDataArray());
        }
    }

    /**
     * @param int $quoteId
     * @param Request $request
     * @param QuoteAccessManager $accessManager
     * @param QuoteRepository $quoteRepository
     * @param AuthorRepository $authorRepository
     * @param Validator $validator
     * @Route(
     *     "/quotes/{quoteId}",
     *     name="saveQuoteAction",
     *     requirements={"quoteId"="\d+"},
     *     methods={"POST"}
     * )
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws HttpJsonException
     * @throws \Doctrine\ORM\ORMException
     */
    public function saveQuoteAction(
        int $quoteId,
        Request $request,
        QuoteAccessManager $accessManager,
        QuoteRepository $quoteRepository,
        AuthorRepository $authorRepository,
        Validator $validator
    ) {
        $em = $this->getDoctrine()->getManager();

        if ($quoteId) {
            $quote = $quoteRepository->find($quoteId);

            if (! $quote) {
                throw new HttpJsonException([
                    "status" => "error",
                    "message" => "Quote not found",
                    "code" => "quote_not_found",
                    "quoteId" => $quoteId,
                ], 404);
            }

            // Throws a forbidden error, in the app doesn't have the access
            $accessManager->writeAccessRequired($quote);
        } else {
            $quote = new Quote();
            $quote->setOwnerApp($accessManager->getClientApp());
            $em->persist($quote);
        }


        $requestData = $request->request->all();
        // Throws a Bad Request error, if validation fails
        $validator->validateQuoteData($requestData);

        $authorName = $requestData['authorName'];
        $text = $requestData['text'];

        $author = $authorRepository->getByName($authorName, $accessManager->getClientApp());

        // TODO: Delete the old author, if he doesn't have associated quotes

        $quote
            ->setAuthor($author)
            ->setText($text)
            ->setLastChangedDateTime(new \DateTime())
        ;

        $em->flush();

        return $this->json($quote->toExpandedDataArray());

    }

    /**
     * @param int $quoteId
     * @param QuoteAccessManager $accessManager
     * @param QuoteRepository $quoteRepository
     * @param AuthorRepository $authorRepository
     * @Route(
     *     "/quotes/{quoteId}",
     *     name="deleteQuoteAction",
     *     requirements={"quoteId"="\d+"},
     *     methods={"DELETE"}
     * )
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws HttpJsonException
     * @throws \Doctrine\ORM\ORMException
     */
    public function deleteQuoteAction(
        int $quoteId,
        QuoteAccessManager $accessManager,
        QuoteRepository $quoteRepository,
        AuthorRepository $authorRepository
    ) {
        $em = $this->getDoctrine()->getManager();

        $quote = $quoteRepository->find($quoteId);

        if (! $quote) {
            throw new HttpJsonException([
                "status" => "error",
                "message" => "Quote not found",
                "code" => "quote_not_found",
                "quoteId" => $quoteId,
            ], 404);
        }

        // Throws a forbidden error, in the app doesn't have the access
        $accessManager->writeAccessRequired($quote);

        $em->remove($quote);
        $em->flush();

        // TODO: Delete the author, if he doesn't have associated quotes

        return $this->json([
            "status" => "ok",
            "message" => "ok"
        ]);

    }
}