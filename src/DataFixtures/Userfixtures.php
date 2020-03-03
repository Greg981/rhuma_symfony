<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Userfixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
         $this->passwordEncoder = $passwordEncoder;
    }
    

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail("toto@tot.fr");
        $user->setRoles(['ROLE_USER']);
        $hashedPassword = $this->passwordEncoder->encodePassword($user, "toto");
        $user->setPassword($hashedPassword);

        $manager->persist($user);
        $manager->flush();
    }
}
