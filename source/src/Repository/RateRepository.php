<?php

namespace App\Repository;

use App\Entity\Rate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Rate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rate[]    findAll()
 * @method Rate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RateRepository extends ServiceEntityRepository
{

    private $sql = <<<EOT
SELECT src.date as DATE, 
src.value as MKD, 
dest.value as EUR, 
src.value / dest.value as RATE
FROM opentag.rate as src
JOIN opentag.rate as dest on src.date = dest.date and dest.code = :to
where  src.code = :from
order by date DESC 
limit 1
EOT;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rate::class);
    }

    public function getRate($from, $to)
    {
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($this->sql);
        $stmt->execute(['from' => $from, 'to' => $to]);
        $rateRecord = $stmt->fetchAllAssociative();
//        dd($rateRecord);
        $trend = doubleval($rateRecord[0]['RATE']) <=> $this->getDeviation($from, $to);
        $sign = '';

        switch ($trend) {
            case -1;
                $sign = '↓';
                break;
            case 0;
                $sign = '-';
                break;
            case 1:
                $sign = '↑';
                break;
        }

        $rateRecord[0]['RATE'] .= ' ' . $sign;
        return $rateRecord[0];
    }

    public function getDeviation($from, $to)
    {
        try {
            $conn = $this->getEntityManager()->getConnection();
            $stmt = $conn->prepare($this->sql . ',10'); // note concatenation
            $stmt->execute(['from' => $from, 'to' => $to]);
            $records = $stmt->fetchAllAssociative();
            $sum = 0;
            foreach ($records as $record) {
                $sum += $record['RATE'];
            }
            return $sum / count($records);
        } catch (\Exception $e) {
            dd($e);
        }


    }
}
