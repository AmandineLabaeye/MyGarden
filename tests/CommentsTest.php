<?php

namespace App\Tests;

use App\Entity\Articles;
use App\Entity\Users;
use PHPUnit\Framework\TestCase;
use App\Entity\Comments;

class CommentsTest extends TestCase
{
    public function testSomething()
    {
        $Comments = (new Comments())
            ->setUsers(new Users())
            ->setArticles(new Articles())
            ->setContent('Content')
            ->setDate('123')
            ->setActive(1);

        $this->assertInstanceOf(Users::class, $Comments->getUsers());
        $this->assertInstanceOf(Articles::class, $Comments->getArticles());
        $this->assertEquals('Content', $Comments->getContent());
        $this->assertEquals('123', $Comments->getDate());
        $this->assertEquals(1, $Comments->getActive());
    }
}
