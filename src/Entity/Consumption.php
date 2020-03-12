<?php

namespace App\Entity;

use App\Messages\CustomApiMessage;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConsumptionRepository")
 */
class Consumption
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Gateway", inversedBy="consumptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $gateway;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Profile", inversedBy="consumptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $profile;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Endpoint", inversedBy="consumptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $endpoint;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Cluster", inversedBy="consumptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cluster;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Attribute", inversedBy="consumptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $attribute;

    /**
     * @ORM\Column(type="integer")
     */
    private $value;

    /**
     * @ORM\Column(type="bigint")
     */
    private $timestamp;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGateway(): ?Gateway
    {
        return $this->gateway;
    }

    public function setGateway(?Gateway $gateway): self
    {
        $this->gateway = $gateway;

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function getEndpoint(): ?Endpoint
    {
        return $this->endpoint;
    }

    public function setEndpoint(?Endpoint $endpoint): self
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    public function getCluster(): ?Cluster
    {
        return $this->cluster;
    }

    public function setCluster(?Cluster $cluster): self
    {
        $this->cluster = $cluster;

        return $this;
    }

    public function getAttribute(): ?Attribute
    {
        return $this->attribute;
    }

    public function setAttribute(?Attribute $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getTimestamp(): ?string
    {
        return $this->timestamp;
    }

    public function setTimestamp(string $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
