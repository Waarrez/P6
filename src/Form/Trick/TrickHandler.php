<?php

namespace App\Form\Trick;

use App\Entity\Trick;
use App\Entity\Image;
use App\Services\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\AbstractUnicodeString;
use Symfony\Component\String\Slugger\AsciiSlugger;

final class TrickHandler
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly PictureService $pictureService
    )
    {
    } // end __construct()

    /**
     * @param array<string, mixed> $options
     * @return FormInterface<string, FormInterface>
     */
    public function prepare(Trick $data = null, array $options = []): FormInterface
    {
        return $this->formFactory->create(TrickFormType::class, $data, $options);
    }

    public function handle(FormInterface $form, Request $request, Trick $trick, string $upload_directory, bool $isEdit = false): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('imageFile')->getData();
            $files = $form->get('images')->getData();

            $name = $form->get('name')->getData();

            $slug = $this->setSlugForName($name);
            $trick->setSlug($slug);

            foreach ($files as $imageForm) {
                $newFileName = $this->pictureService->add($imageForm,'' , 300, 200 );

                try {
                    $image = new Image();
                    $image->setTricks($trick)
                        ->setName($newFileName);

                    $trick->addSecondaryImage($image);

                    $this->entityManager->persist($image);
                } catch (FileException $e) {
                    error_log($e->getMessage());
                }
            }

            if ($file !== null) {
                // Vérifier s'il y a déjà une image associée au trick
                $existingImage = $trick->getImages();

                if ($existingImage !== null) {
                    // Supprimer l'ancienne image du répertoire
                    $existingImagePath = $upload_directory . '/' . $existingImage;
                    if (file_exists($existingImagePath)) {
                        unlink($existingImagePath);
                    }
                }

                $newFileName = uniqid() . '.' . $file->guessExtension();

                try {
                    $file->move($upload_directory, $newFileName);
                } catch (FileException $e) {
                    error_log($e->getMessage());
                }

                $trick->setImages($newFileName);
            }

            $category = $form->get('groups')->getData();
            $trick->setGroups($category);

            if ($isEdit) {
                $trick->setEditAt(new \DateTimeImmutable());
            } else {
                $trick->setCreatedAt(new \DateTimeImmutable());
            }

            $this->entityManager->persist($trick);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    private function setSlugForName(string $slug): AbstractUnicodeString
    {
        $slugger = new AsciiSlugger();
        return $slugger->slug($slug);
    }
}