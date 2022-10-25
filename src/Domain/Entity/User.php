<?php

namespace KarelBartunek\Clockify\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JDecool\Clockify\Model\UserDto;
use KarelBartunek\Clockify\Infrastructure\Repository\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $clockifyId = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\ManyToOne(targetEntity: Workspace::class, cascade: ["all"], fetch: "EAGER", inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'workspaceId', referencedColumnName: 'id')]
    private Workspace|null $workspaceId = null;

    #[ORM\OneToMany(targetEntity: Record::class, mappedBy: 'userId')]
    private Collection $records;

    public function __construct(?UserDto $userDto, ?Workspace $workspaceEntity)
    {
        $this->records = new ArrayCollection();

        if ($userDto && $workspaceEntity) {
            $this
                ->setClockifyId($userDto->id())
                ->setFirstName($userDto->name())
                ->setLastName($userDto->name())
                ->setWorkspace($workspaceEntity);
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getWorkspace(): ?Workspace
    {
        return $this->workspaceId;
    }

    public function setWorkspace(Workspace $workspace): self
    {
        $this->workspaceId = $workspace;

        return $this;
    }
}
