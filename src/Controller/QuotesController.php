<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 2/17/2018
 * Time: 7:44 AM
 */

namespace App\Controller;

use App\Entity\Quote;
use App\Exception\CommonExceptions;
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
        $accessManager->authenticationRequired();

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
        $accessManager->authenticationRequired();

        $quote = $quoteRepository->getRandomQuoteByOwnerApp($accessManager->getClientApp());
        if (!$quote) {
            $this->json([
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
    )
    {
        $author = $authorRepository->find($authorId);
        if (!$author) {
            throw CommonExceptions::createAuthorNotFoundException($authorId);
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
    )
    {
        $accessManager->authenticationRequired();

        $quote = $quoteRepository->find($quoteId);
        if (!$quote) {
            throw CommonExceptions::createQuoteNotFoundException($quoteId);

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
    )
    {
        $accessManager->authenticationRequired();

        $em = $this->getDoctrine()->getManager();

        if ($quoteId) {
            $quote = $quoteRepository->find($quoteId);

            if (!$quote) {
                throw CommonExceptions::createQuoteNotFoundException($quoteId);
            }

            // Throws a forbidden error, in the app doesn't have the access
            $accessManager->writeAccessRequired($quote);
        } else {
            $quote = new Quote();
            $quote->setOwnerApp($accessManager->getClientApp());
            $em->persist($quote);
        }

        $previousAuthor = $quote->getAuthor();

        $requestData = $request->request->all();
        // Throws a Bad Request error, if validation fails
        $validator->validateQuoteData($requestData);

        $authorName = $requestData['authorName'];
        $text = $requestData['text'];

        $author = $authorRepository->getByName($authorName, $accessManager->getClientApp());

        // Delete the previous author, if the are no other quotes under him.
        if ($previousAuthor && ($previousAuthor->getId() !== $author->getId())) {
            $quoteList = array_filter(
                $quoteRepository->findByAuthor($previousAuthor),
                function (Quote $quote) use ($quoteId) {
                    return $quote->getId() != $quoteId;
                }
            );
            if (!count($quoteList)) {
                $em->remove($previousAuthor);
            }
        }

        $quote
            ->setAuthor($author)
            ->setText($text)
            ->setLastChangedDateTime(new \DateTime());

        $em->flush();

        return $this->json($quote->toExpandedDataArray());

    }

    /**
     * @param int $quoteId
     * @param QuoteAccessManager $accessManager
     * @param QuoteRepository $quoteRepository
     * @Route(
     *     "/quotes/{quoteId}",
     *     name="deleteQuoteAction",
     *     requirements={"quoteId"="\d+"},
     *     methods={"DELETE"}
     * )
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws HttpJsonException
     */
    public function deleteQuoteAction(
        int $quoteId,
        QuoteAccessManager $accessManager,
        QuoteRepository $quoteRepository
    )
    {
        $accessManager->authenticationRequired();

        $em = $this->getDoctrine()->getManager();

        $quote = $quoteRepository->find($quoteId);

        if (!$quote) {
            throw CommonExceptions::createQuoteNotFoundException($quoteId);
        }

        // Throws a forbidden error, in the app doesn't have the access
        $accessManager->writeAccessRequired($quote);
        $author = $quote->getAuthor();

        // Delete the author, if the are no other quotes under him.
        if ($author) {
            $quoteList = array_filter(
                $quoteRepository->findByAuthor($author),
                function (Quote $quote) use ($quoteId) {
                    return $quote->getId() != $quoteId;
                }
            );
            if (!count($quoteList)) {
                $em->remove($author);
            }
        }

        $em->remove($quote);

        $em->flush();

        return $this->json([
            "status" => "ok",
            "message" => "ok"
        ]);

    }
}