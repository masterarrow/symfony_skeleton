<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserLoginDTO
{
    #[Assert\NotBlank(message: 'The email field is required')]
    #[Assert\Email(message: 'The email must be a valid email address')]
    public string $email;

    #[Assert\NotBlank(message: 'The password field is required')]
    #[Assert\Length(min: 5, max: 30, minMessage: 'The password must be at least {{ limit }} characters', maxMessage: 'The password must be at most {{ limit }} characters')]
    public string $password;
}
