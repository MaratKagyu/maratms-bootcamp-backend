<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuoteRepository")
 */
class Quote
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id = 0;

    /**
     * @var ClientApp|null
     * @ORM\ManyToOne(targetEntity="\App\Entity\ClientApp")
     * @ORM\JoinColumn(name="owner_app_id", referencedColumnName="id")
     */
    private $ownerApp;

    /**
     * @var Author|null
     * @ORM\ManyToOne(targetEntity="\App\Entity\Author")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $text = '';

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdDateTime;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $lastChangedDateTime;

    /**
     * Quote constructor.
     */
    public function __construct()
    {
        $this->createdDateTime = new \DateTime();
        $this->lastChangedDateTime = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Quote
     */
    public function setId(int $id): Quote
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return ClientApp|null
     */
    public function getOwnerApp(): ?ClientApp
    {
        return $this->ownerApp;
    }

    /**
     * @param ClientApp|null $ownerApp
     * @return Quote
     */
    public function setOwnerApp(?ClientApp $ownerApp): Quote
    {
        $this->ownerApp = $ownerApp;
        return $this;
    }

    /**
     * @return Author|null
     */
    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    /**
     * @param Author|null $author
     * @return Quote
     */
    public function setAuthor(?Author $author): Quote
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return Quote
     */
    public function setText(string $text): Quote
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedDateTime(): \DateTime
    {
        return $this->createdDateTime;
    }

    /**
     * @param \DateTime $createdDateTime
     * @return Quote
     */
    public function setCreatedDateTime(\DateTime $createdDateTime): Quote
    {
        $this->createdDateTime = $createdDateTime;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastChangedDateTime(): \DateTime
    {
        return $this->lastChangedDateTime;
    }

    /**
     * @param \DateTime $lastChangedDateTime
     * @return Quote
     */
    public function setLastChangedDateTime(\DateTime $lastChangedDateTime): Quote
    {
        $this->lastChangedDateTime = $lastChangedDateTime;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            "id" => $this->getId(),
            "ownerAppId" => $this->getOwnerApp()
                ? $this->getOwnerApp()->getId() : 0,
            "authorId" => $this->getAuthor()
                ? $this->getAuthor()->getId() : 0,
            "text" => $this->getText(),
            "createdDateTime" => $this->getCreatedDateTime()
                ? $this->getCreatedDateTime()->format("Y-m-d H:i:s") : '',
            "lastChangedDateTime" => $this->getLastChangedDateTime()
                ? $this->getLastChangedDateTime()->format("Y-m-d H:i:s") : '',
        ];
    }
}
