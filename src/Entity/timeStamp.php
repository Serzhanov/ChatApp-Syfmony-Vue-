<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

trait timeStamp{


    #[ORM\Column(type:"datetime")]
    private $createdAt;

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    #[ORM\prePresist()]
    public function prePresist()
    {
        $this->createdAt=new DateTime();
    }
}