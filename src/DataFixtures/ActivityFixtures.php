<?php

namespace App\DataFixtures;

use App\Entity\Service;
use App\Entity\Activity;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class ActivityFixtures extends Fixture implements OrderedFixtureInterface
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function load(ObjectManager $manager)
    {
        $categories = $this->categoryRepository->findAll();

        foreach ($categories as $category) {
            switch ($category->getName()) {
                case 'Massage':
                    $activity1 = new Activity();
                    $activity1->setName('Massage relaxant Ascoux');
                    $activity1->setDescription('Un massage doux et relaxant pour se sentir bien dans son corps.');
                    $activity1->setPrice(50.00);
                    $activity1->setAddress('1 rue des Massages, 75001 Ascoux');
                    $activity1->setLatitude(48.1045278);
                    $activity1->setLongitude(2.8560571);
                    $activity1->setCategoryId($category); // lier l'activité à la catégorie 'Massage'

                    $activity2 = new Activity();
                    $activity2->setName('Massage sportif Etampe');
                    $activity2->setDescription('Un massage plus profond pour détendre les muscles après l\'effort.');
                    $activity2->setPrice(60.00);
                    $activity2->setAddress('2 rue des Massages, 75001 Paris');
                    $activity2->setLatitude(48.44416949206742);
                    $activity2->setLongitude(2.200397175722377);
                    $activity2->setCategoryId($category); // lier l'activité à la catégorie 'Massage'
                    $manager->persist($activity1);
                    $manager->persist($activity2);
                    break;

                case 'Spa':
                    $activity3 = new Activity();
                    $activity3->setName('Spa détente Evry');
                    $activity3->setDescription('Profitez des bienfaits de l\'eau thermale pour vous relaxer.');
                    $activity3->setPrice(80.00);
                    $activity3->setAddress('3 rue des Spas, 75002 Paris');
                    $activity3->setLatitude(48.622311553880735);
                    $activity3->setLongitude(2.421876852119094);
                    $activity3->setCategoryId($category); // lier l'activité à la catégorie 'Spa'

                    $activity4 = new Activity();
                    $activity4->setName('Spa bien-être Paris');
                    $activity4->setDescription('Prenez soin de votre corps avec les différents soins proposés dans notre spa.');
                    $activity4->setPrice(100.00);
                    $activity4->setAddress('4 rue des Spas, 75002 Paris');
                    $activity4->setLatitude(48.85301745062181);
                    $activity4->setLongitude(2.3691774205693465);
                    $activity4->setCategoryId($category); // lier l'activité à la catégorie 'Spa'

                    $manager->persist($activity3);
                    $manager->persist($activity4);
                    break;

                case 'Salle de sport':
                    $activity5 = new Activity();
                    $activity5->setName('Salle de musculation Pithiviers');
                    $activity5->setDescription('Renforcez vos muscles pour une meilleure santé.');
                    $activity5->setPrice(40.00);
                    $activity5->setAddress('5 rue des Sports, 75003 Pithiviers');
                    $activity5->setLatitude(48.17600673805905);
                    $activity5->setLongitude(2.256948717866266);
                    $activity5->setCategoryId($category); // lier l'activité à la catégorie 'Salle de sport'

                    $activity6 = new Activity();
                    $activity6->setName('Salle de danse MDS');
                    $activity6->setDescription('Express yourself ! Dansez sur vos musiques préférées et ressentez la liberté.');
                    $activity6->setPrice(30.00);
                    $activity6->setAddress('6 rue des Sports, 75003 Paris');
                    $activity6->setLatitude(48.85901341824348);
                    $activity6->setLongitude(2.3750291011138933);
                    $activity6->setCategoryId($category); // lier l'activité à la catégorie 'Salle de sport'

                    $manager->persist($activity5);
                    $manager->persist($activity6);

                    break;

                case 'Salon de beauté':
                    $activity7 = new Activity();
                    $activity7->setName('Soins du visage Opéra');
                    $activity7->setDescription('Offrez-vous un moment de détente avec nos soins du visage professionnels.');
                    $activity7->setPrice(60.00);
                    $activity7->setAddress('7 rue des Salons, 75004 Paris');
                    $activity7->setLatitude(48.86561659919562);
                    $activity7->setLongitude(2.3350409648712707);
                    $activity7->setCategoryId($category); // lier l'activité à la catégorie 'Salon de beauté'


                    $activity8 = new Activity();
                    $activity8->setName('Manucure et pédicure Chatlet');
                    $activity8->setDescription('Prenez soin de vos mains et de vos pieds avec nos services de manucure et pédicure.');
                    $activity8->setPrice(40.00);
                    $activity8->setAddress('8 rue des Salons, 75004 Paris');
                    $activity8->setLatitude(48.859670308425294);
                    $activity8->setLongitude(2.347835970426229);
                    $activity8->setCategoryId($category); // lier l'activité à la catégorie 'Salon de beauté'

                    $manager->persist($activity7);
                    $manager->persist($activity8);
            }
        }
        $manager->flush();

        $service1 = new Service();
        $service1->setName('Massage suédois');
        $service1->setPrice(40.00);
        $service1->setActivityId($activity1);
        $manager->persist($service1);

        $service2 = new Service();
        $service2->setName('Massage aux pierres chaudes');
        $service2->setPrice(45.00);
        $service2->setActivityId($activity1);
        $manager->persist($service2);

        $service3 = new Service();
        $service3->setName('Massage deep tissue');
        $service3->setPrice(55.00);
        $service3->setActivityId($activity2);
        $manager->persist($service3);

        $service4 = new Service();
        $service4->setName('Massage sportif préparation');
        $service4->setPrice(50.00);
        $service4->setActivityId($activity2);
        $manager->persist($service4);
        $service5 = new Service();
        $service5->setName('Accès aux piscines');
        $service5->setPrice(25.00);
        $service5->setActivityId($activity3);
        $manager->persist($service5);

        $service6 = new Service();
        $service6->setName('Sauna et hammam');
        $service6->setPrice(30.00);
        $service6->setActivityId($activity3);
        $manager->persist($service6);

        $service7 = new Service();
        $service7->setName('Massage relaxant');
        $service7->setPrice(50.00);
        $service7->setActivityId($activity4);
        $manager->persist($service7);

        $service8 = new Service();
        $service8->setName('Soins du visage');
        $service8->setPrice(60.00);
        $service8->setActivityId($activity4);
        $manager->persist($service8);
        $service9 = new Service();
        $service9->setName('Soin hydratant');
        $service9->setPrice(50.00);
        $service9->setActivityId($activity7);
        $manager->persist($service9);

        $service10 = new Service();
        $service10->setName('Soin anti-âge');
        $service10->setPrice(70.00);
        $service10->setActivityId($activity7);
        $manager->persist($service10);

        $service11 = new Service();
        $service11->setName('Manucure classique');
        $service11->setPrice(25.00);
        $service11->setActivityId($activity8);
        $manager->persist($service11);

        $service12 = new Service();
        $service12->setName('Pédicure esthétique');
        $service12->setPrice(30.00);
        $service12->setActivityId($activity8);
        $manager->persist($service12);
        $manager->flush();
    }
    public function getOrder()
    {
        return 2;
    }
}
