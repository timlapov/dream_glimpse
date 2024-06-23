<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = json_decode(file_get_contents(__DIR__ . '/../../data/fakePosts.json'), true);
        $faker = Factory::create();

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPassword('admin');
        $admin->setEmail('admin@admin.ru');
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setUsername($faker->userName());
            $user->setPassword('user');
            $user->setEmail($faker->email());

            $manager->persist($user);
            $users[] = $user;
        }

        for ($i = 1; $i <= 10; $i++) {
            foreach ($data as $item) {
                $post = new Post();
                $post->setContent($item['story']);
                $post->setImageUrl($item['image']);
                $post->setCreatedAt($faker->dateTimeBetween('-7 years'));

                $post->setUser($users[array_rand($users)]);

                $manager->persist($post);
            }
        }

        $manager->flush();
    }
}
