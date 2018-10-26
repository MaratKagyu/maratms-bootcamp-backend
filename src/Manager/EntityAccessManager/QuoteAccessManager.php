<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/22/2018
 * Time: 9:07 PM
 */

namespace App\Manager\EntityAccessManager;

use App\Entity\Quote;
use App\Exception\HttpJsonException;
use App\Manager\AccessManager;
use Symfony\Component\HttpFoundation\Response;

class QuoteAccessManager extends AccessManager
{
    /**
     * @param Quote $quote
     * @return bool
     */
    public function clientAppIsAssociatedWithQuote(Quote $quote): bool
    {
        if ($this->getClientApp() && $quote->getOwnerApp()) {
            return ($this->getClientApp()->getId() == $quote->getOwnerApp()->getId());
        }
        return false;
    }

    /**
     * @param Quote $quote
     * @return bool
     */
    public function isReadable(Quote $quote): bool
    {
        return $this->clientAppIsAssociatedWithQuote($quote);
    }

    /**
     * @param Quote $quote
     * @throws HttpJsonException
     * @return bool
     */
    public function readAccessRequired(Quote $quote): bool
    {
        $this->authenticationRequired();

        if (! $this->isReadable($quote)) {
            throw new HttpJsonException([
                "status" => "error",
                "message" => "The app doesn't have the access to the quote",
                "code" => "quote_read_access_forbidden",
                "quoteId" => $quote->getId(),
            ], Response::HTTP_FORBIDDEN);
        }

        return true;
    }

    /**
     * @param Quote $quote
     * @return bool
     */
    public function isWritable(Quote $quote): bool
    {
        return $this->clientAppIsAssociatedWithQuote($quote);
    }

    /**
     * @param Quote $quote
     * @throws HttpJsonException
     * @return bool
     */
    public function writeAccessRequired(Quote $quote): bool
    {
        $this->authenticationRequired();

        if (! $this->isWritable($quote)) {
            throw new HttpJsonException([
                "status" => "error",
                "message" => "The app cannot write to the quote",
                "code" => "quote_write_access_forbidden",
                "quoteId" => $quote->getId(),
            ], Response::HTTP_FORBIDDEN);
        }

        return true;
    }
}