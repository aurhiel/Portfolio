<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TestimonialRepository")
 */
class Testimonial
{
    const SIGN_TYPES = array('names', 'company', 'both');

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $ip;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $agent;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tokenExpiresAt;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $signType;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Client", inversedBy="testimonial")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getAgent(): ?string
    {
        return $this->agent;
    }

    public function setAgent(string $agent): self
    {
        $this->agent = $agent;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getTokenExpiresAt(): ?int
    {
        return $this->tokenExpiresAt;
    }

    public function getSignType(): ?string
    {
        return $this->signType;
    }

    public function getSignTypes()
    {
        return self::SIGN_TYPES;
    }

    public function setSignType(string $signType): self
    {
        $this->signType = $signType;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Generates new token which expires in specified period of time.
     */
    public function generateToken(\DateInterval $interval): string
    {
        $now = new \DateTime();

        $this->token      = Uuid::uuid4()->getHex();
        $this->tokenExpiresAt  = $now->add($interval);

        // Force expire date at the end of the day & then convert to timestamp
        $this->tokenExpiresAt->setTime(23, 59, 59);
        $this->tokenExpiresAt = $this->tokenExpiresAt->getTimestamp();

        return $this->token;
    }

    /**
     * Clears current token.
     */
    public function clearToken(): self
    {
        $this->token      = null;
        $this->tokenExpiresAt  = null;

        return $this;
    }

    /**
     * Checks whether specified reset token is valid.
     */
    public function isTokenValid(string $token): bool
    {
        return
            $this->token === $token   &&
            $this->tokenExpiresAt !== null &&
            $this->tokenExpiresAt > time();
    }

    public function hasExpired(): bool
    {
        return $this->tokenExpiresAt <= time();
    }
}
