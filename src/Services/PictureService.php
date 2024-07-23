<?php

namespace App\Services;

use PHPUnit\Util\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureService
{
    private ParameterBagInterface $params;
    private LoggerInterface $logger;

    public function __construct(ParameterBagInterface $params, LoggerInterface $logger)
    {
        $this->params = $params;
        $this->logger = $logger;
    }

    public function add(UploadedFile $picture, ?string $folder = '', ?int $width = 250, ?int $height = 250): string
    {
        $fichier = md5(uniqid(rand(), true)) . '.webp';

        $picture_infos = getimagesize($picture);

        if($picture_infos === false) {
            throw new Exception('Format incorrect');
        }

        $picture_source = match ($picture_infos['mime']) {
            'image/png' => imagecreatefrompng($picture),
            'image/jpeg' => imagecreatefromjpeg($picture),
            'image/webp' => imagecreatefromwebp($picture),
            default => throw new Exception('Image incorrect'),
        };

        $imageWidth = $picture_infos[0];
        $imageHeight = $picture_infos[1];

        switch ($imageWidth <=> $imageHeight) {
            case -1:
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = ($imageHeight - $squareSize) / 2;
                break;
            case 0:
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = 0;
                break;
            case 1:
                $squareSize = $imageHeight;
                $src_x = ($imageWidth - $squareSize) / 2;
                $src_y = 0;
                break;
        }

        $resized_picture = imagecreatetruecolor($width, $height);

        imagecopyresampled($resized_picture, $picture_source, 0, 0, $src_x, $src_y, $width, $height, $squareSize, $squareSize);

        $path = $this->params->get('upload_original'). $folder;

        if(!file_exists($path . '/tricksImg/')) {
            mkdir($path . '/tricksImg/', 0755, true);
        }

        imagewebp($resized_picture,$path . '/tricksImg/' . $width . 'x' . $height . '-' . $fichier);
        $picture->move($path . '/', $fichier );

        return $fichier;
    }

    public function delete(string $fichier, ?string $folder = '', ?int $width = 250, ?int $height = 250): bool
    {
        $success = false;

        // Base path sans slash final
        $basePath = rtrim($this->params->get('upload_directory'), '/');
        // Dossier spécifique, sans slash initial
        $folderPath = ltrim($folder, '/');

        // Construction du chemin complet
        $path = $basePath . '/' . $folderPath;

        // Construction des chemins pour mini et original
        $mini = $path . '/tricksImg/' . $width . 'x' . $height . '-' . $fichier;
        $original = $path . '/' . $fichier;

        $this->logger->info('Mini file path for deletion:', ['path' => $mini]);
        if (file_exists($mini)) {
            unlink($mini);
            $success = true;
        } else {
            $this->logger->warning('Mini file not found:', ['file' => $mini]);
        }

        $this->logger->info('Original file path for deletion:', ['path' => $original]);
        if (file_exists($original)) {
            unlink($original);
            $success = true;
        } else {
            $this->logger->warning('Original file not found:', ['file' => $original]);
        }

        return $success;
    }
}