<?php

namespace App\Repository;

use App\Entity\User;
use App\DTO\UserDTO;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function saveFromDto(UserDTO $dto, UserPasswordHasherInterface $passwordHasher): User
    {
        $em = $this->getEntityManager();

        if ($dto->getId()) {
            $user = $this->find($dto->getId());
            if (!$user) {
                throw new \Exception('User not found');
            }

            if ($user->getEmail() !== $dto->email) {
                $existing = $this->findOneBy(['email' => $dto->email]);
                if ($existing && $existing->getId() !== $user->getId()) {
                    throw new \RuntimeException('Email already in use');
                }
            }
        } else {
            if ($this->findOneBy(['email' => $dto->email])) {
                throw new \RuntimeException('User with this email already exists');
            }
            $user = new User();
            $em->persist($user);
        }

        $user->setFirstName($dto->firstName);
        $user->setLastName($dto->lastName);
        $user->setEmail($dto->email);
        $user->setPhone($dto->phone ?? '');
        $user->setPhonePrefix($dto->phonePrefix ?? '');
        $user->setCountry($dto->country);

        if (!empty($dto->password)) {
            $hashedPassword = $passwordHasher->hashPassword($user, $dto->password);
            $user->setPassword($hashedPassword);
        } elseif (!$dto->getId()) {
            // При создании пароль обязателен
            throw new \InvalidArgumentException('Password is required for new user');
        }

        $em->flush();

        return $user;
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
