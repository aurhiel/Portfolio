<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Contact
{
    /**
     * @Assert\NotBlank
     */
    protected $lastname;

    /**
     * @Assert\NotBlank
     */
    protected $firstname;

    /**
     * @Assert\NotBlank
     * @Assert\Email
     */
    protected $email;

    /**
     * @Assert\Email
     */
    protected $email_confirm;

    /**
     * @Assert\NotBlank
     */
    protected $message;

    protected $amount;

    protected $project_type;

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEmailConfirm(): ?string
    {
        return $this->email_confirm;
    }

    public function setEmailConfirm(string $email_confirm): self
    {
        $this->email_confirm = $email_confirm;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getProjectType(): ?string
    {
        return $this->project_type;
    }

    public function setProjectType(string $project_type): self
    {
        $this->project_type = $project_type;

        return $this;
    }
}
