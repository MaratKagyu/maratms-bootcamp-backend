<?php
/**
 * Created by PhpStorm.
 * User: MaratMS
 * Date: 10/22/2018
 * Time: 9:07 PM
 */

namespace App\Manager\EntityAccessManager;

use App\Entity\Author;
use App\Exception\HttpJsonException;
use App\Manager\AccessManager;
use Symfony\Component\HttpFoundation\Response;

class AuthorAccessManager extends AccessManager
{
    /**
     * @param Author $author
     * @return bool
     */
    public function clientAppIsAssociatedWithAuthor(Author $author): bool
    {
        if ($this->getClientApp() && $author->getOwnerApp()) {
            return ($this->getClientApp()->getId() == $author->getOwnerApp()->getId());
        }
        return false;
    }

    /**
     * @param Author $author
     * @return bool
     */
    public function isReadable(Author $author): bool
    {
        return $this->clientAppIsAssociatedWithAuthor($author);
    }

    /**
     * @param Author $author
     * @throws HttpJsonException
     * @return bool
     */
    public function readAccessRequired(Author $author): bool
    {
        $this->authenticationRequired();

        if (! $this->isReadable($author)) {
            throw new HttpJsonException([
                "status" => "error",
                "message" => "The app doesn't have the access to the author",
                "code" => "author_read_access_forbidden",
                "authorId" => $author->getId(),
            ], Response::HTTP_FORBIDDEN);
        }

        return true;
    }

    /**
     * @param Author $author
     * @return bool
     */
    public function isWritable(Author $author): bool
    {
        return $this->clientAppIsAssociatedWithAuthor($author);
    }

    /**
     * @param Author $author
     * @throws HttpJsonException
     * @return bool
     */
    public function writeAccessRequired(Author $author): bool
    {
        $this->authenticationRequired();

        if (! $this->isWritable($author)) {
            throw new HttpJsonException([
                "status" => "error",
                "message" => "The app cannot write to the author",
                "code" => "author_write_access_forbidden",
                "authorId" => $author->getId(),
            ], Response::HTTP_FORBIDDEN);
        }

        return true;
    }
}