<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Group;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TricksFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    )
    {
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for($t = 0; $t < 20; $t++) {
            $trick = new Trick();

            $group = new Group();
            $group->setName("Groupe $t");

            $imagePath =__DIR__. '/img/653a8e3f1c035.jpg';
            $nomImage = basename($imagePath);

            $trick->setName("Figure n°$t")
                  ->setCreatedAt(new \DateTimeImmutable())
                  ->setContent("Contenu n°$t" )
                  ->setMedias("https://www.youtube.com/watch?v=EzGPmg4fFL8")
                  ->setImages($nomImage)
                  ->setGroups($group)
                  ->setEditAt(null);

            $comment = new Comment();
            $comment->setTrick($trick)
                    ->setContent("Commentaire $t")
                    ->setUsername($faker->userName);

            $manager->persist($trick);
            $manager->persist($comment);
            $manager->persist($group);
        }

        for($u = 0; $u < 10; $u++) {
            $user = new User();
            $user->setUsername($faker->userName)
              ->setEmail($faker->email)
              ->setConfirmAccount(bin2hex(random_bytes(16)));

            $password = "test";
            $hashPassword = $this->hasher->hashPassword($user ,$password);
            $user->setPassword($hashPassword);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
