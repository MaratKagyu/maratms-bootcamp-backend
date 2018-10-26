<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 * @ORM\Table(name="author",indexes={@ORM\Index(name="name", columns={"name"})})
 */
class Author
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
     * @var string
     * @ORM\Column(type="string")
     */
    private $name = '';

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
     * Author constructor.
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
     * @return Author
     */
    public function setId(int $id): Author
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
     * @return Author
     */
    public function setOwnerApp(?ClientApp $ownerApp): Author
    {
        $this->ownerApp = $ownerApp;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Author
     */
    public function setName(string $name): Author
    {
        $this->name = $name;
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
     * @return Author
     */
    public function setCreatedDateTime(\DateTime $createdDateTime): Author
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
     * @return Author
     */
    public function setLastChangedDateTime(\DateTime $lastChangedDateTime): Author
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
            "name" => $this->getName(),
            "createdDateTime" => $this->getCreatedDateTime()
                ? $this->getCreatedDateTime()->format("Y-m-d H:i:s") : '',
            "lastChangedDateTime" => $this->getLastChangedDateTime()
                ? $this->getLastChangedDateTime()->format("Y-m-d H:i:s") : '',
        ];
    }
}
