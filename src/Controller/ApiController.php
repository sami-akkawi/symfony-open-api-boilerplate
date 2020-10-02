<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class ApiController extends AbstractController
{
    public function handle(): Response
    {
        return $this->render('api/landing-page.html.twig');
    }
}