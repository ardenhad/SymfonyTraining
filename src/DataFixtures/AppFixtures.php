<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        //Changing order due getReference("john_doe") so something has to exist to reference.
        $this->loadUsers($manager);
        $this->loadMicroPosts($manager);
    }

    public function loadMicroPosts(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $microPost = new MicroPost();
            $microPost->setText("Some random text " . rand(0, 100));
            $microPost->setTime(new \DateTime("2018-03-15"));
            $microPost->setUser($this->getReference("john_doe"));
            $manager->persist($microPost);
        }

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername("john_doe");
        $user->setFullName("John Doe");
        $user->setEmail("john.doe@doe.com");
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            "john123")
        );

        $this->addReference("john_doe", $user);

        $manager->persist($user);
        $manager->flush();

    }


}
