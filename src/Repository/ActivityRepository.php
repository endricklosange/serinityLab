<?php

namespace App\Repository;

use App\Entity\Filter;
use App\Entity\Search;
use App\Entity\Activity;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Select;

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
    public function findSearchLocation(Search $search, $userLocation): array
{
    $query = $this->createQueryBuilder('a')
        ->leftJoin('a.category_id', 'c')
        ->select('a, (6371 * ACOS(COS(RADIANS(:latitude)) * COS(RADIANS(a.latitude)) * COS(RADIANS(a.longitude) - RADIANS(:longitude)) + SIN(RADIANS(:latitude)) * SIN(RADIANS(a.latitude)))) AS HIDDEN distance')
        ->where('c.name LIKE :search OR a.name LIKE :search OR a.address LIKE :search')
        ->setParameter('search', '%' . $search . '%')
        ->orderBy('distance', 'ASC')
        ->setParameter('latitude', $userLocation['latitude'])
        ->setParameter('longitude', $userLocation['longitude'])
        ->getQuery();

    return $query->getResult();
}



    public function findFilter(Filter $filter, array $userLocation)
    {
        return $this->getSearchQuery($filter, $userLocation)->getQuery()->getResult();
    }

    public function findMinMax(Filter $filter): array
    {
        $results = $this->getSearchQuery($filter, [], true)
            ->select('MIN(a.price) AS min', 'MAX(a.price) AS max')
            ->getQuery()
            ->getScalarResult();
        return [$results[0]['min'], $results[0]['max']];
    }
    private function getSearchQuery(Filter $filter, array $userLocation = [], $inorePrice = false)
    {
        $queryBuilder = $this->createQueryBuilder('a');

        // Filtre sur les catégories
        if ($filter->getCategories()) {
            $queryBuilder
                ->join('a.category_id', 'c')
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $filter->getCategories());
        }

        // Filtre sur le prix minimum
        if ($filter->getMin() && $inorePrice === false) {
            $queryBuilder
                ->andWhere('a.price >= :minPrice')
                ->setParameter('minPrice', $filter->getMin());
        }

        // Filtre sur le prix maximum
        if ($filter->getMax() && $inorePrice === false) {
            $queryBuilder
                ->andWhere('a.price <= :maxPrice')
                ->setParameter('maxPrice', $filter->getMax());
        }
        if ($filter->getRay()) {
            $queryBuilder
                ->andWhere('(6371 * ACOS(COS(RADIANS(:latitude)) * COS(RADIANS(a.latitude)) * COS(RADIANS(a.longitude) - RADIANS(:longitude)) + SIN(RADIANS(:latitude)) * SIN(RADIANS(a.latitude)))) <= :radius')
                ->setParameter('latitude', $userLocation['latitude'])
                ->setParameter('longitude', $userLocation['longitude'])
                ->setParameter('radius', $filter->getRay());
        }
        if ($filter->getPlaces()) {
            $queryBuilder
                ->andWhere('a.address LIKE :address')
                ->setParameter('address', '%' . $filter->getPlaces() . '%');
        }
        return $queryBuilder;
    }
}
