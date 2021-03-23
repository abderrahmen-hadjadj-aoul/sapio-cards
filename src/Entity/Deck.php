<?php

namespace App\Entity;

use App\Repository\DeckRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeckRepository::class)
 */
class Deck
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="decks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $published = false;

    /**
     * @ORM\OneToMany(targetEntity=Card::class, mappedBy="deck", orphanRemoval=true)
     */
    private $cards;

    /**
     * @ORM\ManyToOne(targetEntity=Deck::class, inversedBy="decks")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity=Deck::class, mappedBy="parent")
     */
    private $decks;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $version;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $last = false;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="favorites")
     */
    private $users;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
        $this->decks = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?user
    {
        return $this->owner;
    }

    public function setOwner(?user $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function toJson($options = null)
    {
        $withCards = isset($options)
          && isset($options["cards"])
          && $options["cards"];
        $withPublishedDecks = isset($options)
          && isset($options["publishedDecks"])
          && $options["publishedDecks"];
        $json = [
            "id" => $this->getId(),
            "name" => $this->name,
            "description" => $this->description,
            "published" => $this->published,
            "version" => $this->version,
        ];
        if ($withCards) {
            $cards = $this->getCards()->toArray();
            $json["cards"] = array_map(fn($card) => $card->toJson(), $cards);
        }
        if ($withPublishedDecks) {
            $publishedDecks = $this->getDecks()->toArray();
            $json["publishedDecks"] = array_map(fn($deck) => $deck->toJson(), $publishedDecks);
        }
        return $json;
    }

    /**
     * @return Collection|Card[]
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(Card $card): self
    {
        if (!$this->cards->contains($card)) {
            $this->cards[] = $card;
            $card->setDeck($this);
        }

        return $this;
    }

    public function removeCard(Card $card): self
    {
        if ($this->cards->removeElement($card)) {
            // set the owning side to null (unless already changed)
            if ($card->getDeck() === $this) {
                $card->setDeck(null);
            }
        }

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getDecks(): Collection
    {
        return $this->decks;
    }

    public function addDeck(self $deck): self
    {
        if (!$this->decks->contains($deck)) {
            $this->decks[] = $deck;
            $deck->setParent($this);
        }

        return $this;
    }

    public function removeDeck(self $deck): self
    {
        if ($this->decks->removeElement($deck)) {
            // set the owning side to null (unless already changed)
            if ($deck->getParent() === $this) {
                $deck->setParent(null);
            }
        }

        return $this;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(?int $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getLast(): ?bool
    {
        return $this->last;
    }

    public function setLast(bool $last): self
    {
        $this->last = $last;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addFavorite($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeFavorite($this);
        }

        return $this;
    }
  
}
