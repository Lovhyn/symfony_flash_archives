<?php

namespace App\DataFixtures;

use App\Entity\Status;
use Faker\Factory;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Tasks;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    /**
     * @var UserPasswordHasherInterface
     */
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {

        //  Création d'un nouvel objet faker.
        $faker = Factory::create('fr_FR');

        //  Création de 5 utilisateurs
        for ($i = 0; $i < 5; $i++) {
            //  Création d'un objet User
            $user = new User;

            //  Hachage mot de passe avec les paramètres de sécurité du $user 
            //  (VOIR DANS /config/packages/security.yaml)
            $hash = $this->encoder->hashPassword($user, "password");
            $user->setPassword($hash)
                ->setIsPrefered(0);


            //  Si le premier utilisateur créé on lui donne le rôle d'admin.
            if ($i === 0) {
                $user->setRoles(["ROLE_ADMIN"])
                    ->setEmail("admin@admin.fr");
            } else {
                $user->setEmail($faker->safeEmail());
            }
            //  On fait persister les données
            $manager->persist($user);
        }

        //  Création de 5 catégories
        for ($i = 0; $i < 5; $i++) {
            //  Nouvel objet Tag
            $tag = new Tag;
            //  Ajoute un nom à notre catégorie
            $tag->setName($faker->colorName());
            //  On fait persister les données.
            $manager->persist($tag);
        }

        // Création des status
        for ($s = 0; $s < 3; $s++) {
            $status = new Status;
            $status->setLabel($s + 1);
            $manager->persist($status);
        }
        //  On push les données dans la base.
        $manager->flush();
        //  Récupération des catégories créées.
        $tags = $manager->getRepository(Tag::class)->findAll();
        $listStatus = $manager->getRepository(Status::class)->findAll();


        $listUsers = $manager->getRepository(User::class)->findAll();

        //  Création entre 15 et 30 tâches aléatoirement
        for ($i = 0; $i < mt_rand(15, 30); $i++) {
            //  Création d'un nouvel objet Task
            $task = new Tasks;
            //  On nourrit l'objet Task
            $task->setName($faker->sentence(6))
                ->setDescription($faker->paragraph(3))
                ->setCreatedAt(new \DateTime())
                ->setDueAt($faker->dateTimeBetween('now', '6 months'))
                ->setTag($faker->randomElement($tags))
                ->setIsArchived(0)
                ->setUser($faker->randomElement($listUsers))
                ->setStatus($faker->randomElement($listStatus));
            //  On fait persister les données
            $manager->persist($task);
        }

        //  On push les données dans la base.
        $manager->flush();
    }
}
