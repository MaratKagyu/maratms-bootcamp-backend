<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/22/2018
 * Time: 9:15 PM
 */

namespace App\Exception;

use Throwable;

class HttpJsonException extends \Exception
{
    /**
     * @var array
     */
    private $response = [];

    /**
     * HttpJsonException constructor.
     * @param array $response
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(array $response, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($response['message'] ?? '', $code, $previous);

        $this->response = $response;
    }

    /**
     * @return array
     */
    public function getResponse(): array
    {
        return $this->response;
    }
}