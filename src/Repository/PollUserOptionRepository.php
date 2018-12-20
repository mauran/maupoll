<?php

namespace App\Repository;

use App\Entity\PollUserOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PollUserOption|null find($id, $lockMode = null, $lockVersion = null)
 * @method PollUserOption|null findOneBy(array $criteria, array $orderBy = null)
 * @method PollUserOption[]    findAll()
 * @method PollUserOption[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PollUserOptionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PollUserOption::class);
    }


    public function getActiveVotesForUser($user, $pollId) {
        return $this->createQueryBuilder('pu')
            ->leftJoin('pu.PollOptionId', 'pollOption')
            ->leftJoin('pollOption.poll', 'poll')
            ->where('poll.id = :pollId')
            ->setParameter('pollId', $pollId)
            ->andWhere('pu.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return PollUserOption[] Returns an array of PollUserOption objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PollUserOption
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
