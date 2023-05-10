<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private const NB_ARTICLES = 120;
    private const NB_CATEGORIES = 12;
    private const NB_REGULAR_TEST_USERS = 8;

    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        
        for($i=0, $categories=[]; $i<self::NB_CATEGORIES; $i++)
        {
            $category = new Category();
            $category
            ->setName($faker->words(3,true))
            ->setDescription($faker->realTextBetween(100,200));
            $categories[]=$category;

            $manager->persist($category);
        }

        for($i=0, $authors=[]; $i<self::NB_REGULAR_TEST_USERS; $i++)
        {
            $author= new User();
            $author
            ->setEmail($faker->userName().'@gmail.com')
            ->setPassword($this->hasher->hashPassword($author, 'regular'));
            $authors[]=$author;

            $manager->persist($author);
        }

        for($i=0; $i<self::NB_ARTICLES; $i++)
        {
            $article = New Article();
            $article
            ->setTitle($faker->jobTitle())
            ->setContent($faker->realTextBetween(200,400))
            ->setVisible($faker->boolean(80))
            ->setDateCreated($faker->dateTimeBetween('-3 years'))
            ->setCategory($categories[$faker->numberBetween(0, count($categories)-1)])
            ->setAuthor($authors[$faker->numberBetween(0, count($authors)-1)]);
            $manager->persist($article);
        }

        $admin = new User();
        $admin
        ->setPassword($this->hasher->hashPassword($admin,'admin'))
        ->setEmail('admin@boss.com')
        ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $manager->flush();
    }
}
