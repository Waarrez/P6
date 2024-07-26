<?php

namespace App\Form\Trick;

use App\Entity\Trick;
use App\Entity\Image;
use App\Services\PictureService;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\AbstractUnicodeString;
use Symfony\Component\String\Slugger\AsciiSlugger;

final class TrickHandler
{
    private RequestStack $requestStack;

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly PictureService $pictureService,
        RequestStack $requestStack
    )
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param array<string, mixed> $options
     * @return FormInterface<string, FormInterface>
     */
    public function prepare(Trick $data = null, array $options = []): FormInterface
    {
        return $this->formFactory->create(TrickFormType::class, $data, $options);
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     * @param Trick $trick
     * @param string $upload_directory
     * @param bool $isEdit
     * @return bool
     */
    public function handle(FormInterface $form, Request $request, Trick $trick, string $upload_directory, bool $isEdit = false): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('imageFile')->getData();
            $files = $form->get('images')->getData();
            $secondaryPictures = $form->get('secondaryImages')->getData();

            $name = $form->get('name')->getData();
            $slug = $this->setSlugForName($name);
            $trick->setSlug($slug);

            foreach ($secondaryPictures as $secondary) {
                if ($secondary->getFile() !== null) {
                    foreach ($trick->getSecondaryImages() as $img) {
                        if ($secondary->getName() === $img->getName()) {
                            $newFileName = $this->pictureService->add($secondary->getFile(), '', 300, 200);
                            try {
                                $this->pictureService->delete($img->getName(), 'tricksImg', 300, 200);

                                $this->entityManager->remove($img);

                                $this->entityManager->flush();

                                $image = new Image();
                                $image->setTricks($trick)
                                    ->setName($newFileName);

                                $trick->addSecondaryImage($image);
                                $this->entityManager->persist($image);
                                $this->entityManager->flush();
                            } catch (FileException $e) {
                                error_log($e->getMessage());
                            }
                        }
                    }
                }
            }

            foreach ($files as $imageForm) {
                $newFileName = $this->pictureService->add($imageForm, '', 300, 200);

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
                $existingImage = $trick->getImages();

                if (!empty($existingImage)) {
                    $existingImagePath = $upload_directory . '/' . $existingImage;

                    if (file_exists($existingImagePath) && is_file($existingImagePath)) {
                        unlink($existingImagePath);
                    }
                }

                $newFileName = uniqid() . '.' . $file->guessExtension();

                try {
                    $file->move($upload_directory, $newFileName);
                    $trick->setImages($newFileName);
                } catch (FileException $e) {
                    error_log($e->getMessage());
                }
            }

            $category = $form->get('groups')->getData();
            $trick->setGroups($category);

            if ($isEdit) {
                $trick->setEditAt(new \DateTimeImmutable());
            } else {
                $trick->setCreatedAt(new \DateTimeImmutable());
            }

            try {
                $this->entityManager->persist($trick);
                $this->entityManager->flush();
            } catch (UniqueConstraintViolationException $e) {
                $this->requestStack->getSession()->getFlashBag()->add('error', 'Un trick avec ce nom existe déjà.');
                error_log('UniqueConstraintViolationException: ' . $e->getMessage());
                return false;
            } catch (DriverException $e) {
                // Catch database related exceptions
                error_log('DriverException: ' . $e->getMessage());
                $this->requestStack->getSession()->getFlashBag()->add('error', 'Erreur de base de données.');
                return false;
            } catch (\Exception $e) {
                error_log('Exception: ' . $e->getMessage());
                $this->requestStack->getSession()->getFlashBag()->add('error', 'Erreur inconnue lors de la sauvegarde du trick.');
                return false;
            }

            return true;
        }

        return false;
    }


    /**
     * @param string $slug
     * @return AbstractUnicodeString
     */
    private function setSlugForName(string $slug): AbstractUnicodeString
    {
        $slugger = new AsciiSlugger();
        return $slugger->slug($slug);
    }
}
