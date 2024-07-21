<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\Trick\TrickHandler;
use App\Repository\GroupRepository;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Services\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    public function __construct(
        private readonly TrickRepository        $trickRepository,
        private readonly TrickHandler           $trickHandler,
        private readonly ImageRepository        $imageRepository,
        private readonly GroupRepository        $groupRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly Filesystem             $filesystem,
    ) {}

    /**
     * @return Response
     */
    #[Route('/tricks', name: 'tricks')]
    public function tricks(): Response
    {
        $tricks = $this->trickRepository->findAll();

        return $this->render('tricks/tricks.html.twig', [
            'tricks' => $tricks,
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/addTrick', name: 'addTrick')]
    public function addTrick(Request $request): Response
    {
        $trick = new Trick();
        $form = $this->trickHandler->prepare($trick);

        if ($this->trickHandler->handle($form, $request, $trick, $this->getParameter("upload_directory"))) {
            $this->addFlash("success", "La figure à bien été ajouté !");
            return $this->redirectToRoute('tricks');
        }

        return $this->render('tricks/add_trick.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param string $slug
     * @return Response
     * @throws NonUniqueResultException
     */
    #[Route('/trick/detail/{slug}', name: "home.viewTrick")]
    public function viewTrick(string $slug): Response
    {
        $trick = $this->trickRepository->getTrickBySlug($slug);

        if($trick->getComments() && $trick) {
            $comments = $trick->getComments();
        } else {
            $comments = [];
        }

        return $this->render('tricks/view_trick.html.twig', [
            'trick' => $trick,
            'comments' => $comments,
        ]);
    }

    /**
     * @param string $slug
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    #[Route('/trick/edit/{slug}', name: "editTrick")]
    public function editTrick(string $slug, Request $request): Response
    {
        $trick = $this->trickRepository->getTrickBySlug($slug);
        $groups = $this->groupRepository->findAll();

        if (!$trick instanceof Trick) {
            $this->addFlash("error", "La figure avec le slug '$slug' n'existe pas.");
            return $this->redirectToRoute('tricks');
        }

        $form = $this->trickHandler->prepare($trick);

        if ($this->trickHandler->handle($form, $request, $trick, $this->getParameter("upload_directory"))) {
            $this->addFlash("success", "La figure à bien été modifié !");
            return $this->redirectToRoute('tricks');
        }

        return $this->render('tricks/edit_trick.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
            'groups' => $groups
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
            return $this->redirectToRoute('tricks');
        }

        $comments = $trick->getComments();
        if ($comments) {
            foreach ($comments as $comment) {
                $this->entityManager->remove($comment);
            }
        }

        $uploadDirectory = $this->getParameter('upload_directory');
        $imagePath = $uploadDirectory . '/' . $trick->getImages();

        if ($this->filesystem->exists($imagePath)) {
            try {
                $this->filesystem->remove($imagePath);
            } catch (IOExceptionInterface) {
                $this->addFlash("error", "Une erreur est survenue lors de la suppression de l'image.");
                return $this->redirectToRoute('home.index');
            }
        }

        $this->entityManager->remove($trick);
        $this->entityManager->flush();

        $this->addFlash("success", "La figure a été supprimée avec succès !");
        return $this->redirectToRoute('tricks');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function addComment(Request $request): JsonResponse
    {
        /** @var $user User */
        $user = $this->getUser();
        $content = $request->request->get('content');
        $slug = $request->request->get('slug');
        $trick = $this->trickRepository->getTrickBySlug($slug);

        if (!$trick) {
            return new JsonResponse([
                'message' => 'Trick non trouvé',
            ], 404);
        }

        $comment = new Comment();
        $comment->setContent($content);
        $comment->setTrick($trick);
        $comment->setUsers($user);

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        $updatedComments = [];
        foreach ($trick->getComments() as $comment) {
            $user = $comment->getUsers();
            $userPicture = $user->getUserPicture();
            $imgPath = $userPicture && $userPicture !== "" ? 'uploads/pictures/' . $userPicture : 'img/default-picture.png';

            $updatedComments[] = [
                'username' => $user->getUsername(),
                'content' => $comment->getContent(),
                'userPicture' => $imgPath,
            ];
        }

        return new JsonResponse([
            'message' => 'Commentaire ajouté avec succès',
            'comments' => $updatedComments,
        ]);
    }

    /**
     * @param string $id
     * @param Request $request
     * @param PictureService $pictureService
     * @return JsonResponse
     */
    #[Route('/trick/removeImage/{id}', name: "deleteImage", methods: ['DELETE'])]
    public function deleteImage(string $id, Request $request, PictureService $pictureService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['_token'])) {
            return new JsonResponse(['error' => 'Token non fourni'], 400);
        }

        if (!preg_match('/^[a-zA-Z0-9\-]+$/', $id)) {
            return new JsonResponse(['error' => 'ID invalide'], 400);
        }

        $image = $this->imageRepository->find($id);

        if (!$image) {
            return new JsonResponse(['error' => 'Image non trouvée'], 404);
        }

        // Vérifiez le token CSRF en utilisant l'ID validé
        $csrfToken = 'delete' . $id;
        if ($this->isCsrfTokenValid($csrfToken, $data['_token'])) {
            $name = $image->getName();

            try {
                if ($pictureService->delete($name, 'tricksImg', 300, 200)) {
                    $this->entityManager->remove($image);
                    $this->entityManager->flush();

                    return new JsonResponse(['success' => 'Image supprimée'], 200);
                } else {
                    return new JsonResponse(['error' => 'Erreur de suppression'], 400);
                }
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Erreur de suppression: ' . $e->getMessage()], 500);
            }
        } else {
            return new JsonResponse(['error' => 'Token invalide'], 400);
        }
    }
}
