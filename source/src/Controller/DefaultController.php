<?php

namespace App\Controller;

use App\Entity\Rate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /**
     * @Route("/")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->json(['ok' => 1]);
    }

    /**
     * @Route("/migration")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function migration()
    {
        $process = new Process(['php', 'bin/console', 'doctrine:migrations:migrate', '--no-interaction', '-vv'], '/var/www/html');
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $this->json(['output' => $process->getOutput()]);
    }

    /**
     * @Route("/seed")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function seed()
    {
        $process = new Process(['php', 'bin/console', 'doctrine:fixtures:load', '-n'], '/var/www/html');
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $this->json(['output' => $process->getOutput()]);
    }


    /**
     * @Route("/rate/{from}/{to}")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rate($from, $to)
    {

        $from = strtoupper($from);
        $to = strtoupper($to);
        $x = $this->getDoctrine()
            ->getRepository(Rate::class)
            ->getRate($from, $to);
        dd($x);
        return $this->json(['rate' => rand()]);
    }


}
