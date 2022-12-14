<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\DonorRepository;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: DonorRepository::class)]
#[ApiResource(
    //types: ['https://schema.org/DonateAction'],    
    mercure: true,
    paginationClientItemsPerPage: true,
    //routePrefix: '/api'
)]
#[GetCollection]
#[Post]
#[Get]
#[Put]
#[Patch]
#[Delete]
#[Put(
    output: false,
    messenger: true,
)]
class Donor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    //#[ApiProperty(types: ['https://schema.org/agent'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    /**
     * Doações.
     */
    #[ORM\OneToMany(mappedBy: 'donor', targetEntity: Donation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]    
    private Collection $donations;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Doações.
     */
    public function addDonation(Donation $donation, bool $updateRelation = true): void
    {
        if ($this->donations->contains($donation)) {
            return;
        }

        $this->donations->add($donation);
        if ($updateRelation) {
            $donation->setDonor($this, false);
        }
    }

    public function removeDonation(Donation $donation, bool $updateRelation = true): void
    {
        $this->donations->removeElement($donation);
        if ($updateRelation) {
            $donation->setDonor(null, false);
        }
    }

    /**
     * @return Collection<int, Donation>
     */
    public function getDonations(): iterable
    {
        return $this->donations;
    }
}
