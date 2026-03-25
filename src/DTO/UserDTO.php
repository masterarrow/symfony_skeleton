<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserDTO
{
    #[Assert\When(
        expression: 'this.getId() !== null',
        constraints: [new Assert\Positive()]
    )]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'The first_name field is required')]
    #[Assert\Length(min: 2, max: 200, minMessage: 'The first_name must be at least {{ limit }} characters', maxMessage: 'The first_name must be at most {{ limit }} characters')]
    public string $firstName;

    #[Assert\NotBlank(message: 'The last_name field is required')]
    #[Assert\Length(min: 2, max: 200, minMessage: 'The last_name must be at least {{ limit }} characters', maxMessage: 'The last_name must be at most {{ limit }} characters')]
    public string $lastName;

    #[Assert\NotBlank(message: 'The email field is required')]
    #[Assert\Email(message: 'The email must be a valid email address')]
    public string $email;

    #[Assert\NotBlank(message: 'The password field is required')]
    #[Assert\Length(min: 5, max: 30, minMessage: 'The password must be at least {{ limit }} characters', maxMessage: 'The password must be at most {{ limit }} characters')]
    public string $password;

    public ?array $roles = ['ROLE_USER'];

    #[Assert\Length(min: 9, max: 12)]
    public string $phone = '';

    #[Assert\Length(min: 2, max: 5)]
    public string $phonePrefix = '';

    #[Assert\NotBlank(message: 'The country field is required')]
    #[Assert\Length(min: 2, max: 2)]
    public string $country;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }
}
