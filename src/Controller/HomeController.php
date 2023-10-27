<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickFormType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TrickRepository $trickRepository
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

    #[Route('/addTrick', name: 'home.addTrick')]
    public function addTrick(Request $request) : Response {

        $trick = new Trick();
        $newFileName = null;

        $form = $this->createForm(TrickFormType::class, $trick);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('imageFile')->getData();

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
            }
        }

        return $this->render('tricks/add_trick.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/trick/detail/{id}', name: "home.viewTrick")]
    public function viewTrick(string $id) : Response {

        $trick = $this->trickRepository->find($id);

        return $this->render('tricks/view_trick.html.twig', [
            'trick' => $trick
        ]);
    }

    #[Route('/trick/edit/{id}', name: "home.editTrick")]
    public function editTrick(string $id, Request $request) : Response {

        $trick = $this->trickRepository->find($id);

        $form = $this->createForm(TrickFormType::class, $trick);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
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

        $imagePath = $this->getParameter('upload_directory') . '/' . $trick->getImages();

        if (file_exists($imagePath)) {
            // Supprimez l'image du dossier
            unlink($imagePath);
        }


        $this->entityManager->remove($trick);
        $this->entityManager->flush();

        return $this->redirectToRoute('home.index');

    }
}
