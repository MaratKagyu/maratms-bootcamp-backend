<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/22/2018
 * Time: 11:31 PM
 */

namespace App\Service;

use App\Exception\HttpJsonException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Validator
 * @package App\Service
 */
class Validator
{

    /**
     * @param array $quoteData
     * @throws HttpJsonException
     * @return bool
     */
    public function validateQuoteData(array $quoteData): bool
    {
        $authorName = $quoteData['authorName'] ?? "";
        $text = $quoteData['text'] ?? "";

        if (! ($authorName && $text)) {
            throw new HttpJsonException([
                "status" => "error",
                "message" => "Author name and text are mandatory",
                "code" => "empty_author_name_or_text",
                "authorName" => $authorName,
                "text" => $text,
            ], Response::HTTP_BAD_REQUEST);
        }

        if (strlen($authorName) > 255) {
            throw new HttpJsonException([
                "status" => "error",
                "message" => "Author name is too long (up to 255 bytes is allowed)",
                "code" => "author_name_too_long",
                // "authorName" => $authorName,
            ], Response::HTTP_BAD_REQUEST);
        }

        if (strlen($text) > 2048) {
            throw new HttpJsonException([
                "status" => "error",
                "message" => "Text is too long (up to 2048 bytes is allowed)",
                "code" => "text_is_too_long",
            ], Response::HTTP_BAD_REQUEST);
        }

        return true;
    }
}