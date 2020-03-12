<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GatewayRepository")
 */
class Gateway
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true, length=16)
     */
    private $eui;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Consumption", mappedBy="gateway")
     */
    private $consumptions;

    public function __construct(string $eui) {
        $this->eui = $eui;
        $this->consumptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEui(): ?string
    {
        return $this->eui;
    }

    public function setEui(string $eui): self
    {
        $this->eui = $eui;

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
            $consumption->setGateway($this);
        }

        return $this;
    }

    public function removeConsumption(Consumption $consumption): self
    {
        if ($this->consumptions->contains($consumption)) {
            $this->consumptions->removeElement($consumption);
            // set the owning side to null (unless already changed)
            if ($consumption->getGateway() === $this) {
                $consumption->setGateway(null);
            }
        }

        return $this;
    }
}
