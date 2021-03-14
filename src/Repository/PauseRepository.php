<?php

namespace App\Repository;

use App\Entity\Pause;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pause|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pause|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pause[]    findAll()
 * @method Pause[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PauseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pause::class);
    }
}
