<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use App\Entity\Tasks;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        //  Création d'un nouvel objet faker.
        $faker = Factory::create('fr_FR');

        //  Création de 5 catégories
        for ($i = 0; $i < 5; $i++) {
            //  Nouvel objet Tag
            $tag = new Tag;
            //  Ajoute un nom à notre catégorie
            $tag->setName($faker->colorName());
            //  On fait persister les données.
            $manager->persist($tag);
        }
        //  On push les données dans la base.
        $manager->flush();
        //  Récupération des catégories créées.
        $tags = $manager->getRepository(Tag::class)->findAll();

        //  Création entre 15 et 30 tâches aléatoirement
        for ($i = 0; $i < mt_rand(15, 30); $i++) {
            //  Création d'un nouvel objet Task
            $task = new Tasks;
            //  On nourrit l'objet Task
            $task->setName($faker->sentence(6))
                ->setDescription($faker->paragraph(3))
                ->setCreatedAt(new \DateTime())
                ->setDueAt($faker->dateTimeBetween('now', '6 months'))
                ->setTag($faker->randomElement($tags));
            //  On fait persister les données
            $manager->persist($task);
        }
        //  On push les données dans la base.
        $manager->flush();
    }
}
