<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Images;
use App\Form\Trick\TrickFormType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly TrickRepository $trickRepository,
    )
    {}

    /**
     * @return Response
     */
    #[Route('/', name: 'home.index')]
    public function index(): Response
    {
        $tricks = $this->trickRepository->findAll();

        return $this->render('home/index.html.twig', [
            'tricks' => $tricks
        ]);
    }
}
