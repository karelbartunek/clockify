<?php

namespace KarelBartunek\Clockify\Infrastructure\Repository;

use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use KarelBartunek\Clockify\Domain\Entity\Record;

/**
 * @extends ServiceEntityRepository<Record>
 *
 * @method Record|null find($id, $lockMode = null, $lockVersion = null)
 * @method Record|null findOneBy(array $criteria, array $orderBy = null)
 * @method Record[]    findAll()
 * @method Record[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Record::class);
    }

    public function save(Record $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Record $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getCountExportedInDateRange(DateTimeImmutable $from, DateTimeImmutable $to): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.exported = :is_exported')
            ->andWhere('r.date_start >= :date_start')
            ->andWhere('r.date_end <= :date_end')
            ->setParameter('is_exported', 1)
            ->setParameter('date_start', $from)
            ->setParameter('date_end', $to)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findNotExportedRecords(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        return $this->createQueryBuilder('r')
            ->select('
                IDENTITY(r.userId) AS userId,
                SUM(r.duration) AS duration,
                COUNT(r.id) AS total,
                MAX(r.isStandup) AS isStandup,
                u.firstName,
                u.lastName'
            )
            ->andWhere('r.exported = :is_exported')
            ->andWhere('r.date_start >= :date_start')
            ->andWhere('r.date_end <= :date_end')
            ->innerJoin('r.userId', 'u')
            ->setParameter('is_exported', 0)
            ->setParameter('date_start', $from)
            ->setParameter('date_end', $to)
            ->groupBy('r.userId')
            ->getQuery()
            ->getResult();
    }

    public function markExportedRecords(DateTimeImmutable $from, DateTimeImmutable $to): int
    {
        return $this->createQueryBuilder('r')
            ->update(Record::class, 'r')
            ->set('r.exported', 1)
            ->andWhere('r.date_start >= :date_start')
            ->andWhere('r.date_end <= :date_end')
            ->setParameter('date_start', $from)
            ->setParameter('date_end', $to)
            ->getQuery()
            ->execute();
    }
}
