<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/26/2018
 * Time: 10:44 AM
 */

namespace App\Tests\Service;

use App\Exception\HttpJsonException;
use App\Service\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{

    /**
     * @throws HttpJsonException
     */
    public function testCorrectQuoteData()
    {
        $validator = new Validator();

        $data = [
            "authorName" => "Author name",
            "text" => "Quote text"
        ];

        $this->assertTrue($validator->validateQuoteData($data));
    }

    /**
     * @throws HttpJsonException
     */
    public function testIncorrectAuthor()
    {
        $this->expectException(HttpJsonException::class);
        $validator = new Validator();

        $data = [
            "authorName" => str_repeat("Z", 256),
            "text" => "Quote text"
        ];

        $this->assertTrue($validator->validateQuoteData($data));
    }

    /**
     * @throws HttpJsonException
     */
    public function testEmptyAuthor()
    {
        $this->expectException(HttpJsonException::class);
        $validator = new Validator();

        $data = [
            "authorName" => "",
            "text" => "Quote text"
        ];

        $this->assertTrue($validator->validateQuoteData($data));
    }

    /**
     * @throws HttpJsonException
     */
    public function testIncorrectQuoteText()
    {
        $this->expectException(HttpJsonException::class);
        $validator = new Validator();

        $data = [
            "authorName" => "Author name",
            "text" => str_repeat("Z", 2049),
        ];

        $this->assertTrue($validator->validateQuoteData($data));
    }

    /**
     * @throws HttpJsonException
     */
    public function testEmptyQuoteExt()
    {
        $this->expectException(HttpJsonException::class);
        $validator = new Validator();

        $data = [
            "authorName" => "Author name",
            "text" => ""
        ];

        $this->assertTrue($validator->validateQuoteData($data));
    }
}