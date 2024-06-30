<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Images;
use App\Entity\Trick;
use App\Form\Trick\TrickFormType;
use App\Form\Trick\TrickHandler;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{

    public function __construct(
        private readonly TrickRepository $trickRepository,
        private readonly TrickHandler $trickHandler,
        private readonly EntityManagerInterface $entityManager,
        private readonly Filesystem $filesystem
    )
    {}

    /**
     * @return Response
     */
    #[Route('/tricks', name: 'tricks')]
    public function tricks(): Response
    {
        $tricks = $this->trickRepository->findAll();

        return $this->render('tricks/tricks.html.twig', [
            'tricks' => $tricks
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/addTrick', name: 'addTrick')]
    public function addTrick(Request $request) : Response {
        $trick = new Trick();
        $form = $this->trickHandler->prepare($trick);

        if ($this->trickHandler->handle($form, $request, $trick, $this->getParameter("upload_directory"))) {
            return $this->redirectToRoute('home.index');
        }

        return $this->render('tricks/add_trick.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param string $id
     * @return Response
     */
    #[Route('/trick/detail/{id}', name: "home.viewTrick")]
    public function viewTrick(string $id) : Response {

        $trick = $this->trickRepository->find($id);

        $comments = $trick->getComments();

        return $this->render('tricks/view_trick.html.twig', [
            'trick' => $trick,
            'comments' => $comments
        ]);
    }

    /**
     * @param string $id
     * @param Request $request
     * @return Response
     */
    #[Route('/trick/edit/{id}', name: "editTrick")]
    public function editTrick(string $id, Request $request) : Response {
        $trick = $this->trickRepository->find($id);

        if (!$trick) {
            $this->addFlash("error", "La figure n'existe pas !");
            $this->redirectToRoute("home.index");
        }

        $form = $this->trickHandler->prepare($trick);

        if ($this->trickHandler->handle($form, $request, $trick, $this->getParameter("upload_directory"), true)) {
            return $this->redirectToRoute('home.index');
        }

        return $this->render('tricks/edit_trick.html.twig', [
            'form'  => $form->createView()
        ]);
    }

    /**
     * @param string $id
     * @return Response
     */
    #[Route('/trick/remove/{id}', name: "deleteTrick")]
    public function deleteTrick(string $id): Response
    {
        $trick = $this->trickRepository->find($id);

        if (!$trick) {
            $this->addFlash("error", "La figure n'existe pas !");
            return $this->redirectToRoute('home.index');
        }

        $comments = $trick->getComments();

        if ($comments) {
            foreach ($comments as $comment) {
                $this->entityManager->remove($comment);
            }
        }

        $imagePath = $this->getParameter('upload_directory') . '/' . $trick->getImages();

        $this->filesystem->remove($imagePath);

        $this->entityManager->remove($trick);
        $this->entityManager->flush();

        $this->addFlash("success", "La figure a été supprimée avec succès !");
        return $this->redirectToRoute('home.index');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addComment(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $content = $request->request->get('content');
        $id = $request->request->get('id');
        $trick = $this->trickRepository->find($id);

        $comment = new Comment();
        $comment->setContent($content);
        $comment->setTrick($trick);
        $comment->setUsername($user->getUsername());

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        $updatedComments = [];
        foreach ($trick->getComments() as $comment) {
            $updatedComments[] = [
                'username' => $comment->getUsername(),
                'content' => $comment->getContent(),
            ];
        }

        return new JsonResponse([
            'message' => 'Commentaire ajouté avec succès',
            'comments' => $updatedComments,
        ]);
    }
}
