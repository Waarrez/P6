<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route('/error404', name: 'error404')]
    public function error404(): Response
    {
        return $this->render('error/error404.html.twig');
    }

    /**
     * Handle HTTP exceptions and return an appropriate error response.
     *
     * @param HttpExceptionInterface $exception The exception that triggered the error.
     * @return Response The response object with the rendered error page.
     */
    public function error(HttpExceptionInterface $exception): Response
    {
        $statusCode = $exception->getStatusCode();

        $message = match ($statusCode) {
            404 => 'La page demandée n\'existe pas.',
            500 => 'Une erreur interne du serveur est survenue. Veuillez réessayer plus tard.',
            default => $exception->getMessage(),
        };

        return $this->render('error/error.html.twig', [
            'message'     => $message,
            'status_code' => $statusCode,
        ]);
    }
}
