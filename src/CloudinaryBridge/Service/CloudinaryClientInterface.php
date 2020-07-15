<?php

namespace App\CloudinaryBridge\Service;

interface CloudinaryClientInterface
{
    public function upload(string $filePath, array $options = []);

    public function delete(string $publicId): void;
}
