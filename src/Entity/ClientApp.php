<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TODO: Add indexes
 * @ORM\Entity(repositoryClass="App\Repository\ClientAppRepository")
 */
class ClientApp
{
    const APP_TYPE_WORDPRESS = 0;

    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     */
    private $id = 0;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $name = "";

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $token = "";

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $type = self::APP_TYPE_WORDPRESS;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createDateTime;

    /**
     * ClientApp constructor.
     */
    public function __construct()
    {
        $this->createDateTime = new \DateTime();
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
     * @return ClientApp
     */
    public function setId(int $id): ClientApp
    {
        $this->id = $id;
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
     * @return ClientApp
     */
    public function setName(string $name): ClientApp
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return ClientApp
     */
    public function setToken(string $token): ClientApp
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return ClientApp
     */
    public function setType(int $type): ClientApp
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreateDateTime(): \DateTime
    {
        return $this->createDateTime;
    }

    /**
     * @param \DateTime $createDateTime
     * @return ClientApp
     */
    public function setCreateDateTime(\DateTime $createDateTime): ClientApp
    {
        $this->createDateTime = $createDateTime;
        return $this;
    }


}
