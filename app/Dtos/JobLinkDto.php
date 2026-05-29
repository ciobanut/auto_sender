<?php

namespace App\Dtos;

class JobLinkDto
{
    public function __construct(
        public readonly string $jobUrl,
        public readonly string $title,
        public readonly string $companyName,
        public readonly ?string $location = null,
        public readonly ?string $shortPreview = null,
        public readonly ?string $externalJobId = null,
        public readonly ?string $publicationDate = null,
    ) {}
}
