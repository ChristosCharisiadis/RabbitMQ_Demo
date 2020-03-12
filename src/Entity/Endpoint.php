<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EndpointRepository")
 */
class Endpoint
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Consumption", mappedBy="endpoint")
     */
    private $consumptions;

    public function __construct(int $id) {
        $this->id = $id;
        $this->consumptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Collection|Consumption[]
     */
    public function getConsumptions(): Collection
    {
        return $this->consumptions;
    }

    public function addConsumption(Consumption $consumption): self
    {
        if (!$this->consumptions->contains($consumption)) {
            $this->consumptions[] = $consumption;
            $consumption->setEndpoint($this);
        }

        return $this;
    }

    public function removeConsumption(Consumption $consumption): self
    {
        if ($this->consumptions->contains($consumption)) {
            $this->consumptions->removeElement($consumption);
            // set the owning side to null (unless already changed)
            if ($consumption->getEndpoint() === $this) {
                $consumption->setEndpoint(null);
            }
        }

        return $this;
    }
}
