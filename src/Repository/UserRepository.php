<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Ramsey\Uuid\Uuid;
use Webauthn\PublicKeyCredentialUserEntity;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneByUsername(string $username): ?User
    {
        $qb = $this->createQueryBuilder('user');

        return $qb
            ->where('user.name = :name')
            ->setParameter(':name', $username)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByUserHandle(string $userHandle): ?User
    {
        $qb = $this->createQueryBuilder('user');

        return $qb
            ->where('user.user_handle = :user_handle')
            ->setParameter(':user_handle', $userHandle)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function createUserEntity(string $username,string $displayName,?string $icon) : PublicKeyCredentialUserEntity
    {
        return new User(Uuid::getFactory()->uuid4()->toString(), $username, $displayName, ['ROLE_USER']);
    }
}
