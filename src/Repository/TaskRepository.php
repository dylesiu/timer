<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @return Task[]
     */
    public function findTasks(User $user, array $states, string $start = null, string $end = null): array
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.state in (:states)')
            ->andWhere('t.user = :user')
            ->setParameter('user', $user)
            ->setParameter('states', $states)
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(100);

        if ($start) {
            $start = new \DateTime($start);
            $qb->andWhere('t.start >= :start')->setParameter('start', $start->format('Y-m-d 00:00:00'));
        }

        if ($end) {
            $end = new \DateTime($end);
            $qb->andWhere('t.end <= :end')->setParameter('end', $end->format('Y-m-d 23:59:59'));
        }

        return $qb->getQuery()->getResult();
    }
}
