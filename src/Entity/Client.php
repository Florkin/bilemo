<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 */
class Client extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=ClientUser::class, mappedBy="client", orphanRemoval=true)
     */
    private $users;

    public function __construct()
    {
        $this->users = new PersistentCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return PersistentCollection
     */
    public function getUsers(): PersistentCollection
    {
        return $this->users;
    }

    public function addUser(ClientUser $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setClient($this);
        }

        return $this;
    }

    public function removeUser(ClientUser $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getClient() === $this) {
                $user->setClient(null);
            }
        }

        return $this;
    }
}
