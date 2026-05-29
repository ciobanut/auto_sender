<?php

namespace App\Dtos;

class JobDetailDto
{
    public function __construct(
        public readonly ?string $fullDescription = null,
        public readonly ?array $technologies = null,
        public readonly ?int $salaryFrom = null,
        public readonly ?int $salaryTo = null,
        public readonly ?string $salaryCurrency = null,
        public readonly ?string $companyName = null,
        public readonly ?string $contactEmail = null,
        public readonly ?string $recruiterName = null,
        public readonly ?string $phone = null,
        public readonly ?array $requirements = null,
        public readonly ?array $responsibilities = null,
        public readonly ?string $seniority = null,
        public readonly ?string $workType = null,
        public readonly ?string $publicationDate = null,
    ) {}
}
