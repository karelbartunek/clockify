<?php

namespace KarelBartunek\Clockify\Domain\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JDecool\Clockify\Model\TimeEntryDtoImpl;
use KarelBartunek\Clockify\Infrastructure\Repository\RecordRepository;

#[ORM\Entity(repositoryClass: RecordRepository::class)]
class Record
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $clockifyId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateStart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEnd = null;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ["all"], fetch: "EAGER", inversedBy: 'records')]
    #[ORM\JoinColumn(name: 'userId', referencedColumnName: 'id')]
    private User|null $userId = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $exported = false;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\Column]
    private ?bool $isStandup = null;

    public function __construct(?TimeEntryDtoImpl $timeEntryDto, ?User $userEntity, ?array $standUpProjectIds)
    {
        if ($timeEntryDto && $userEntity) {
            $duration = $timeEntryDto->timeInterval()->end()->getTimestamp() - $timeEntryDto->timeInterval()->start()->getTimestamp();

            $isStandUp = $timeEntryDto->projectId() && in_array($timeEntryDto->projectId(), $standUpProjectIds);

            $this
                ->setClockifyId($timeEntryDto->id())
                ->setDateStart($timeEntryDto->timeInterval()->start())
                ->setDateEnd($timeEntryDto->timeInterval()->end())
                ->setDuration($duration)
                ->setIsStandup($isStandUp)
                ->setUser($userEntity);
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

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->userId;
    }

    public function setUser(User $user): self
    {
        $this->userId = $user;

        return $this;
    }

    public function isExported(): bool
    {
        return $this->exported;
    }

    public function setExported(bool $exported): Record
    {
        $this->exported = $exported;
        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function isIsStandup(): ?bool
    {
        return $this->isStandup;
    }

    public function setIsStandup(bool $isStandup): self
    {
        $this->isStandup = $isStandup;

        return $this;
    }
}
