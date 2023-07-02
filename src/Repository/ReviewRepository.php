<?php

namespace App\Repository;

use App\Entity\Activity;
use PDO;
use PDOException;
use App\Entity\Review;
use App\Service\PdoConnexionService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Review>
 *
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    private $pdoConnexionService;
    public function __construct(ManagerRegistry $registry, PdoConnexionService $pdoConnexionService)
    {
        parent::__construct($registry, Review::class);
        $this->pdoConnexionService = $pdoConnexionService;
    }

    public function save(Review $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Review $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function all(): array
    {
        $pdo = $this->pdoConnexionService->getConnection();
        $stmt = $pdo->prepare('SELECT * FROM `review`');
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function findByActivityId(Activity $data): ?array
    {
        $pdo = $this->pdoConnexionService->getConnection();
        $stmt = $pdo->prepare('SELECT r.*, u.last_name, u.first_name,
                                     s.price_quality, s.cleanliness, s.location, s.product,
                                     (s.price_quality + s.cleanliness + s.location + s.product) / 4 as overall_average
                               FROM review r
                               JOIN `user` u ON r.user_id = u.id
                               JOIN `score` s ON r.score_id = s.id
                               WHERE `activity_id` = :id');
        $stmt->bindValue(':id', $data->getId(), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function createReviewWithScore(Review $data): bool
    {
        $pdo = $this->pdoConnexionService->getConnection();
        $pdo->beginTransaction();

        try {
            // Insertion des données dans la table "score"
            $stmtScore = $pdo->prepare('INSERT INTO `score` (`price_quality`,`cleanliness`,`location`,`product`) VALUES (:price_quality, :cleanliness, :location, :product)');
            $stmtScore->bindValue(':price_quality', $data->getScore()->getPriceQuality(), PDO::PARAM_INT);
            $stmtScore->bindValue(':cleanliness', $data->getScore()->getCleanliness(), PDO::PARAM_INT);
            $stmtScore->bindValue(':location', $data->getScore()->getLocation(), PDO::PARAM_INT);
            $stmtScore->bindValue(':product', $data->getScore()->getProduct(), PDO::PARAM_INT);
            $stmtScore->execute();

            // Récupération de l'ID de la nouvelle entrée dans "score"
            $scoreId = $pdo->lastInsertId();

            // Insertion des données dans la table "review" avec la référence à l'ID de "score"
            $stmtReview = $pdo->prepare('INSERT INTO `review` (`activity_id`, `user_id`, `score_id`, `comment`, `created_at`) VALUES (:activity_id, :user_id, :score_id, :comment, :created_at)');
            $stmtReview->bindValue(':activity_id', $data->getActivity()->getId(), PDO::PARAM_INT);
            $stmtReview->bindValue(':user_id', $data->getUser()->getId(), PDO::PARAM_INT);
            $stmtReview->bindValue(':score_id', $scoreId, PDO::PARAM_INT);
            $stmtReview->bindValue(':comment', $data->getComment(), PDO::PARAM_STR);
            $stmtReview->bindValue(':created_at', $data->getCreatedAt()->format('Y-m-d H:i:s'), PDO::PARAM_STR);


            $stmtReview->execute();

            // Valider la transaction
            $pdo->commit();

            return true;
        } catch (PDOException $e) {
            // En cas d'erreur, annuler la transaction
            $pdo->rollBack();
            return false;
        }
    }


    public function updateReviewWithScore(Review $data): bool
    {
        $pdo = $this->pdoConnexionService->getConnection();
        $pdo->beginTransaction();
        $scoreId = $data->getScore()->getID();
        try {
            // Vérifier si le score_id a été modifié
            if (isset($scoreId)) {
                // Mettre à jour la table "score" avec la nouvelle valeur
                $stmtScore = $pdo->prepare('UPDATE `score` SET `price_quality` = :price_quality, `cleanliness` = :cleanliness, `location` = :location, `product` = :product WHERE `id` = :score_id');
                $stmtScore->bindValue(':price_quality', $data->getScore()->getPriceQuality(), PDO::PARAM_INT);
                $stmtScore->bindValue(':cleanliness', $data->getScore()->getCleanliness(), PDO::PARAM_INT);
                $stmtScore->bindValue(':location', $data->getScore()->getLocation(), PDO::PARAM_INT);
                $stmtScore->bindValue(':product', $data->getScore()->getProduct(), PDO::PARAM_INT);
                $stmtScore->bindValue(':score_id', $scoreId, PDO::PARAM_INT);
                $stmtScore->execute();
            }

            // Mettre à jour la table "review" avec les autres données
            $stmtReview = $pdo->prepare('UPDATE `review` SET `activity_id` = :activity_id, `user_id` = :user_id, `comment` = :comment, `score_id` = :score_id WHERE `id` = :id');
            $stmtReview->bindValue(':activity_id', $data->getActivity()->getId(), PDO::PARAM_INT);
            $stmtReview->bindValue(':user_id', $data->getUser()->getId(), PDO::PARAM_INT);
            $stmtReview->bindValue(':score_id', $scoreId, PDO::PARAM_INT);
            $stmtReview->bindValue(':comment', $data->getComment(), PDO::PARAM_STR);
            $stmtReview->bindValue(':id', $data->getId(), PDO::PARAM_INT);
            $stmtReview->execute();

            // Valider la transaction
            $pdo->commit();

            return true;
        } catch (PDOException $e) {
            // En cas d'erreur, annuler la transaction
            $pdo->rollBack();
            return false;
        }
    }

    public function deleteReview(Review $data): bool
    {
        $pdo = $this->pdoConnexionService->getConnection();
        $stmt = $pdo->prepare('DELETE FROM `review` WHERE `id` = :id');
        $stmt->bindValue(':id', $data->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }





    //    /**
    //     * @return Review[] Returns an array of Review objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Review
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
