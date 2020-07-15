<?php

namespace App\CloudinaryBridge\Service;

interface MediaUploaderInterface
{
    public function upload(string $filePath, array $options = []);
}
