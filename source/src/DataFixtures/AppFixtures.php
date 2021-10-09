<?php

namespace App\DataFixtures;

use App\Entity\Rate;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\KernelInterface;

class AppFixtures extends Fixture
{

    /**
     * @var KernelInterface
     */
    private $kernel;
    /**
     * @var CarbonInterface
     */
    private $carbon;

    public function __construct(KernelInterface $kernel)
    {
        ;
        $this->kernel = $kernel;

    }

    public function load(ObjectManager $manager)
    {
        // php bin/console doctrine:fixtures:load -n

        try {

            $filePath = implode(DIRECTORY_SEPARATOR, [$this->kernel->getProjectDir(), 'DOCS', 'example-response.json']);
            $obj = json_decode(file_get_contents($filePath), true);

            $date = Carbon::now();

            for ($i = 0; $i < 32; $i++) {

                $date->addDay(-1);

                foreach ($obj['rates'] as $code => $value) {
                    $percent = 1 + rand(1, 5) / 100;
                    $calNewValue = rand(0, 1) ? $value * $percent : $value / $percent;

                    $rate = new Rate();
                    $rate->setCode($code);
                    $rate->setValue($calNewValue);
                    $rate->setDate($date);
                    $manager->persist($rate);
                }
                $manager->flush();
            }


        } catch (\Exception $e) {
            dd($e->getMessage());
            dd('Cant find seeder file');
        }

    }
}
