<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Images;
use App\Entity\Trick;
use App\Form\TrickFormType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TrickRepository $trickRepository,
        private readonly CommentRepository $commentRepository
    )
    {}

    #[Route('/', name: 'home.index')]
    public function index(): Response
    {

        $tricks = $this->trickRepository->findAll();

        return $this->render('home/index.html.twig', [
            'tricks' => $tricks
        ]);
    }

    #[Route('/tricks', name: 'home.tricks')]
    public function tricks(): Response
    {

        $tricks = $this->trickRepository->findAll();

        return $this->render('tricks/tricks.html.twig', [
            'tricks' => $tricks
        ]);
    }

    #[Route('/addTrick', name: 'home.addTrick')]
    public function addTrick(Request $request) : Response {

        $trick = new Trick();
        $newFileName = null;

        $form = $this->createForm(TrickFormType::class, $trick);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('imageFile')->getData();
            $files = $form->get('images')->getData();

            foreach ($files as $images) {
                $newFileName = uniqid().'.'.$images->guessExtension();

                try {
                    $images->move($this->getParameter("upload_directory"), $newFileName);

                    $imagesTricks = new Images();
                    $imagesTricks->setTricks($trick)
                        ->setName($newFileName);

                    $trick->addImagesTrick($imagesTricks);

                    $this->entityManager->persist($imagesTricks);
                } catch (\Exception $e) {
                    // Handle the exception, log or throw it
                }
            }

            if($file) {
                $uploadDirectory = $this->getParameter("upload_directory");
                $newFileName = uniqid().'.'.$file->guessExtension();

                try {
                    $file->move($uploadDirectory, $newFileName);
                } catch (\Exception $e) {

                }

                $category = $form->get('groups')->getData();
                $trick->setImages($newFileName);
                $trick->setGroups($category);
                $trick->setCreatedAt(new \DateTimeImmutable());

                $this->entityManager->persist($trick);
                $this->entityManager->flush();
                return $this->redirectToRoute('home.index');
            }
        }

        return $this->render('tricks/add_trick.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/trick/detail/{id}', name: "home.viewTrick")]
    public function viewTrick(string $id, Request $request, Security $security) : Response {

        $trick = $this->trickRepository->find($id);

        $comments = $trick->getComments();

        return $this->render('tricks/view_trick.html.twig', [
            'trick' => $trick,
            'comments' => $comments
        ]);
    }

    #[Route('/trick/edit/{id}', name: "home.editTrick")]
    public function editTrick(string $id, Request $request) : Response {

        $trick = $this->trickRepository->find($id);

        $form = $this->createForm(TrickFormType::class, $trick);

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {
            $files = $form->get('images')->getData();

            foreach ($files as $images) {
                $newFileName = uniqid().'.'.$images->guessExtension();

                try {
                    $images->move($this->getParameter("upload_directory"), $newFileName);

                    $imagesTricks = new Images();
                    $imagesTricks->setTricks($trick)
                        ->setName($newFileName);

                    $trick->addImagesTrick($imagesTricks);

                    $this->entityManager->persist($imagesTricks);
                } catch (\Exception $e) {
                    // Handle the exception, log or throw it
                }
            }

            if($trick->getImages() !== null) {
                $file = $form->get('imageFile')->getData();
                if($file) {
                    $uploadDirectory = $this->getParameter("upload_directory");
                    $newFileName = uniqid().'.'.$file->guessExtension();
                    try {
                        $file->move($uploadDirectory, $newFileName);
                    } catch (\Exception $e) {

                    }
                    $trick->setImages($newFileName);
                }
            }

            $trick->setEditAt(new \DateTimeImmutable());
            $this->entityManager->persist($trick);
            $this->entityManager->flush();

            return $this->redirectToRoute('home.index');
        }

        return $this->render('tricks/edit_trick.html.twig', [
            'form'  => $form
        ]);

    }

    #[Route('/trick/remove/{id}', name: "home.deleteTrick")]
    public function deleteTrick(string $id) : Response {

        $trick = $this->trickRepository->find($id);

        foreach ($trick->getComments() as $comment) {
            $trick->removeComment($comment);
        }

        $imagePath = $this->getParameter('upload_directory') . '/' . $trick->getImages();

        if (file_exists($imagePath)) {
            // Supprimez l'image du dossier
            unlink($imagePath);
        }


        $this->entityManager->remove($trick);
        $this->entityManager->flush();

        return $this->redirectToRoute('home.index');
    }

    public function addComment(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $content = $request->request->get('content');
        $id = $request->request->get('id');
        $trick = $this->trickRepository->find($id);

        // Créez une nouvelle instance de l'entité Comment et définissez ses propriétés
        $comment = new Comment();
        $comment->setContent($content);
        $comment->setTrick($trick);
        $comment->setUsername($user->getUsername());

        // Enregistrez le commentaire dans la base de données
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
