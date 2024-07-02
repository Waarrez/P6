<?php

namespace App\Form\Trick;

use App\Entity\Trick;
use App\Entity\Images;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class TrickHandler
{
    public function __construct(private FormFactoryInterface $formFactory, private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param array<string, mixed> $options
     * @return FormInterface<string, FormInterface>
     */
    public function prepare(Trick $data = null, array $options = []): FormInterface {
        return $this->formFactory->create(TrickFormType::class, $data, $options);
    }

    public function handle(FormInterface $form, Request $request, Trick $trick, string $upload_directory, bool $isEdit = false): bool {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('imageFile')->getData();
            $files = $form->get('images')->getData();

            foreach ($files as $images) {
                $newFileName = uniqid().'.'.$images->guessExtension();

                try {
                    $images->move($upload_directory, $newFileName);

                    $imagesTricks = new Images();
                    $imagesTricks->setTricks($trick)
                        ->setName($newFileName);

                    $trick->addImagesTrick($imagesTricks);

                    $this->entityManager->persist($imagesTricks);
                } catch (FileException) {
                    // Handle the exception, log or throw it
                }
            }

            if ($file) {
                $newFileName = uniqid().'.'.$file->guessExtension();

                try {
                    $file->move($upload_directory, $newFileName);
                } catch (FileException) {
                    // Handle the exception, log or throw it
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
}
