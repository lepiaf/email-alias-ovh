<?php

namespace App\Repository;

use App\Entity\PublicKeyCredentialSource;
use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Webauthn\Bundle\Repository\PublicKeyCredentialSourceRepository as BasePublicKeyCredentialSourceRepository;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicKeyCredentialSourceRepository extends BasePublicKeyCredentialSourceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublicKeyCredentialSource::class);
    }

    /**
     * @return PublicKeyCredentialSource[]
     */
    public function allForUser(PublicKeyCredentialUserEntity $user): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb->select('c')
            ->from($this->getClass(), 'c')
            ->where('c.userHandle = :user_handle')
            ->setParameter(':user_handle', $user->getId())
            ->getQuery()
            ->execute()
            ;
    }
}
