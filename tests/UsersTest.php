<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Users;

class UsersTest extends TestCase
{
    public function testSomething()
    {
        $Users = (new Users())
            ->setAvatar('Be')
            ->setName('Bo')
            ->setSurname('Ba')
            ->setEmail('Bi')
            ->setAge(1)
            ->setRegion('Bu')
            ->setVille('Bh')
            ->setUsername('Bm')
            ->setApropos('Bz')
            ->setWork('Bw')
            ->setRank('Dz')
            ->setActive(1)
            ->setVerif('De');

        $this->assertEquals('Be', $Users->getAvatar());
        $this->assertEquals('Bo', $Users->getName());
        $this->assertEquals('Ba', $Users->getSurname());
        $this->assertEquals('Bi', $Users->getEmail());
        $this->assertEquals(1, $Users->getAge());
        $this->assertEquals('Bu', $Users->getRegion());
        $this->assertEquals('Bh', $Users->getVille());
        $this->assertEquals('Bm', $Users->getUsername());
        $this->assertEquals('Bz', $Users->getApropos());
        $this->assertEquals('Bw', $Users->getWork());
        $this->assertEquals('Dz', $Users->getRank());
        $this->assertEquals(1, $Users->getActive());
        $this->assertEquals('De', $Users->getVerif());
    }
}
