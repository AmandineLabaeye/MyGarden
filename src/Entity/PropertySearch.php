<?php

namespace App\Entity;

class PropertySearch
{
    /**
     * @var string|null
     */
    private $nameArticle;

    /**
     * @return string|null
     */
    public function getNameArticle(): ?string
    {
        return $this->nameArticle;
    }

    /**
     * @param string|null $nameArticle
     * @return PropertySearch
     */
    public function setNameArticle(string $nameArticle): PropertySearch
    {
        $this->nameArticle = $nameArticle;
        return $this;
    }
}