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

//            $filePath = implode(DIRECTORY_SEPARATOR, [$this->kernel->getProjectDir(), 'DOCS', 'example-response.json']);
//            $obj = json_decode(file_get_contents($filePath), true);

            $date = Carbon::now();

            for ($i = 0; $i < 31; $i++) {


                $url = sprintf('https://openexchangerates.org/api/historical/%s.json?app_id=42683a64679d4bf78c84419c9a2607ef',$date->toDateString());
                $date->addDay(-1);
                $obj = json_decode(file_get_contents($url), true);
                foreach ($obj['rates'] as $code => $value) {
                    $rate = new Rate();
                    $rate->setCode($code);
                    $rate->setValue($value);
                    $rate->setDate($date);
                    $manager->persist($rate);
                }
                $manager->flush();
            }


        } catch (\Exception $e) {
            dd($e);
            dd('Cant find seeder file');
        }

    }
}
