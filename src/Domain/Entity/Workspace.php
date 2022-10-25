<?php

namespace KarelBartunek\Clockify\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JDecool\Clockify\Model\WorkspaceDto;
use KarelBartunek\Clockify\Infrastructure\Repository\WorkspaceRepository;

#[ORM\Entity(repositoryClass: WorkspaceRepository::class)]
class Workspace
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $clockifyId = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'workspaceId')]
    private Collection $users;

    public function __construct(?WorkspaceDto $workspaceDto)
    {
        $this->users = new ArrayCollection();

        if ($workspaceDto) {
            $this
                ->setClockifyId($workspaceDto->id())
                ->setName($workspaceDto->name());
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClockifyId(): ?string
    {
        return $this->clockifyId;
    }

    public function setClockifyId(string $clockifyId): self
    {
        $this->clockifyId = $clockifyId;

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
}
