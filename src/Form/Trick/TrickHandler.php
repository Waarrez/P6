<?php

namespace App\Form\Trick;

use App\Entity\Trick;
use App\Entity\Image;
use App\Entity\Video;
use App\Services\PictureService;
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
            $newVideos = [
                $form->get('newVideo')->getData(),
                $form->get('newVideo2')->getData(),
                $form->get('newVideo3')->getData()
            ];

            $name = $form->get('name')->getData();
            $slug = $this->setSlugForName($name);
            $trick->setSlug($slug);

            if (count($files) >= 4) {
                $session = $this->requestStack->getSession();
                $session->getFlashBag()->add('error', 'Vous ne pouvez mettre que 3 images');
                return false;
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

                if ($existingImage !== null) {
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

            // Ajouter les vidéos
            foreach ($newVideos as $videoUrl) {
                if ($videoUrl) { // Assurez-vous que l'URL de la vidéo n'est pas vide
                    $video = new Video();
                    $video->setTricks($trick)
                        ->setUrl($videoUrl);

                    $trick->addVideo($video);
                    $this->entityManager->persist($video);
                }
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
