<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
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
class Product
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
     * @ORM\Column(type="string", length=100)
     */
    private $name;
     /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\Regex(
     *     pattern="/^[a-z0-9]+$/i",
     *     match=true,
     *     message="No puede contener caracteres especiales"
     * )
     */
    private $description;
     /**
     * @ORM\Column(type="string", length=100)
     */
    private $make;
     /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="products")
     */
    private $category;
     /**
     * @ORM\Column(type="float")
     */
    private $price;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
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

    public function getMake()
    {
        return $this->make;
    }

    public function setMake($make)
    {
        $this->make = $make;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }
}
