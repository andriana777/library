<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=AuthorRepository::class)
 */
class Author
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=55)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=55)
     * @Assert\Length(
     *     min = 3,
     *     minMessage = "The lastname should contain min 3 letters"
     * )
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=55, nullable=true)
     */
    private $fathername;

    /**
     * @ORM\ManyToMany(targetEntity=Book::class, inversedBy="authors")
     *  #[JoinTable(name: 'author_book')]
     */
    private $books;

    public function __construct(array $data)
    {
        $this->firstname =$data['firstname'];
        $this->lastname =$data['lastname'];
        $this->fathername =$data['fathername'];
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getFathername(): ?string
    {
        return $this->fathername;
    }

    public function setFathername(?string $fathername): self
    {
        $this->fathername = $fathername;

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        $this->books->removeElement($book);

        return $this;
    }
}
