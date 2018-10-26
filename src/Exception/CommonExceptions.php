<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/26/2018
 * Time: 3:14 PM
 */

namespace App\Exception;


class CommonExceptions
{
    /**
     * @param int $quoteId
     * @return HttpJsonException
     */
    public static function createQuoteNotFoundException($quoteId)
    {
        return new HttpJsonException([
            "status" => "error",
            "message" => "Quote not found",
            "code" => "quote_not_found",
            "quoteId" => $quoteId,
        ], 404);
    }


    /**
     * @param int $authorId
     * @return HttpJsonException
     */
    public static function createAuthorNotFoundException($authorId)
    {
        return new HttpJsonException([
            "status" => "error",
            "message" => "Author not found",
            "code" => "author_not_found",
            "authorId" => $authorId,
        ], 404);
    }
}