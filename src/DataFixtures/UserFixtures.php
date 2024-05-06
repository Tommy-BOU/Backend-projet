<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private CategoryRepository $catRepo, private UserPasswordHasherInterface $userPasswordHasher)
    {
    }
    public function load(ObjectManager $manager): void
    {

        $admin = new User();

        $admin->setUsername('admin');
        $admin->setRoles(['ROLE_USER','ROLE_ADMIN']);
        $admin->setEmail('admin@admin.com');
        $admin->setUserName('admin');
        $admin->setPassword($this->userPasswordHasher->hashPassword(
            $admin,
            'admin'
        ));

        $manager->persist($admin);

        $manager->flush();
    }
}
