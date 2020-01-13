<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @UniqueEntity(
 *     fields={"code"},
 *     errorPath="code",
 *     message="El Codigo ya esta en uso."
 * )
 * @UniqueEntity(
 *     fields={"name"},
 *     errorPath="name",
 *     message="El Nombre ya esta en uso."
 * )
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
     /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\Regex(
     *     pattern="/^[a-z0-9]+$/i",
     *     match=true,
     *     message="No puede contener caracteres especiales"
     * )
     */
    private $code;
     /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $name;
     /**
     * @ORM\Column(type="string", length=100)
     */
    private $description;
     /**
     * @ORM\Column(type="boolean")
     */
    private $active;
     /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="category")
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }
    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }
    public function __ToString() {
        return $this->name;
    }
}
