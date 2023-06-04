<?php

namespace App\Repository;

use App\Entity\Search;
use App\Entity\Activity;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Activity>
 *
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    public function save(Activity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Activity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByLocation($latitude, $longitude)
    {
        $qb = $this->createQueryBuilder('a');

        // Calcul de la distance entre la position de l'utilisateur et les activités
        $qb->select('a')
            ->addSelect('(6371 * ACOS(COS(RADIANS(:latitude)) * COS(RADIANS(a.latitude)) * COS(RADIANS(a.longitude) - RADIANS(:longitude)) + SIN(RADIANS(:latitude)) * SIN(RADIANS(a.latitude)))) AS HIDDEN distance')
            ->setParameter('latitude', $latitude)
            ->setParameter('longitude', $longitude)
            ->orderBy('distance');
        return $qb->getQuery()->getResult();
    }
   public function findSearch(Search $search): array
{
    $query = $this->createQueryBuilder('a')
        ->leftJoin('a.category_id', 'c')
        ->where('c.name LIKE :search OR a.name LIKE :search OR a.address LIKE :search')
        ->setParameter('search', '%' . $search . '%')
        ->getQuery();

    return $query->getResult();
}

}
