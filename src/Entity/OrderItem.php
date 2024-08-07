<?php

declare (strict_types=1);

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Bolt\Entity\Content;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Content $product = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $orderRef = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getProduct(): ?Content {
        return $this->product;
    }

    public function setProduct(?Content $product): static {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?int {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static {
        $this->quantity = $quantity;

        return $this;
    }

    public function getOrderRef(): ?Order {
        return $this->orderRef;
    }

    public function setOrderRef(?Order $orderRef): static {
        $this->orderRef = $orderRef;

        return $this;
    }

    /**
     * Tests if the given item given corresponds to the same order item.
     *
     * @param OrderItem $item
     *
     * @return bool
     */
    public function equals(OrderItem $item): bool {
        return $this->getProduct()->getId() === $item->getProduct()->getId();
    }

    /**
     * Calculates the item total.
     *
     * @return float|int
     */
    public function getTotal(): float {
        return $this->getProduct()->getFieldValue('price1') * $this->getQuantity();
    }
}
