<?php

namespace App\Services;

use App\Dtos\JobDetailDto;
use App\Dtos\JobLinkDto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class RabotaMdScraper
{
    private const BASE_URL = 'https://www.rabota.md';

    public function fetchJobs(string $keyword): Collection
    {
        $url = self::BASE_URL.'/ro/jobs-moldova-'.$keyword;

        $response = Http::retry(3, 2000)
            ->timeout(15)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml',
                'Accept-Language' => 'ro-RO,ro;q=0.9,en;q=0.8',
            ])
            ->get($url);

        if (! $response->successful()) {
            return collect();
        }

        return $this->parseJobListings($response->body(), $keyword);
    }

    public function fetchJobDetails(string $url): ?JobDetailDto
    {
        $response = Http::retry(3, 2000)
            ->timeout(15)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml',
            ])
            ->get($url);

        if (! $response->successful()) {
            return null;
        }

        return $this->parseJobDetail($response->body());
    }

    private function parseJobListings(string $html, string $keyword): Collection
    {
        $jobs = collect();

        // Simple regex-based extraction for Rabota.md listing pages.
        // Each job listing is typically in an <article> or <div> with class containing "job".
        preg_match_all('/<a[^>]*href="(\/ro\/job\/[^"]+)"[^>]*>(.*?)<\/a>/s', $html, $matches, PREG_SET_ORDER);

        $seen = [];

        foreach ($matches as $match) {
            $relativeUrl = $match[1];
            $fullUrl = self::BASE_URL.$relativeUrl;

            if (isset($seen[$fullUrl]) || count($seen) >= 20) {
                continue;
            }

            $seen[$fullUrl] = true;

            // Extract title from the link content
            $title = strip_tags($match[2]);
            $title = trim(preg_replace('/\s+/', ' ', $title));

            if (empty($title) || strlen($title) < 5) {
                continue;
            }

            // Try to extract external_job_id from URL
            preg_match('/\/job\/(\d+)/', $relativeUrl, $idMatch);
            $externalJobId = $idMatch[1] ?? null;

            $jobs->push(new JobLinkDto(
                jobUrl: $fullUrl,
                title: $title,
                companyName: $this->extractCompanyName($html, $relativeUrl),
                location: 'Moldova',
                shortPreview: null,
                externalJobId: $externalJobId,
            ));
        }

        return $jobs;
    }

    private function parseJobDetail(string $html): ?JobDetailDto
    {
        // Full description extraction
        $fullDescription = $this->extractBetween($html, 'class="description"', '/section');

        if (! $fullDescription) {
            // Fallback: try to extract all text content
            $fullDescription = strip_tags($html);
            $fullDescription = trim(preg_replace('/\s+/', ' ', $fullDescription));
        }

        if (empty($fullDescription) || strlen($fullDescription) < 20) {
            return null;
        }

        // Extract known technologies from the description
        $technologies = $this->extractTechnologies($fullDescription);

        // Extract salary
        $salary = $this->extractSalary($html);

        // Detect work type
        $workType = $this->detectWorkType($fullDescription);

        // Detect seniority
        $seniority = $this->detectSeniority($fullDescription);

        // Extract company name
        $companyName = $this->extractCompanyNameFromDetail($html);

        // Extract contact email
        $contactEmail = $this->extractEmail($fullDescription);

        return new JobDetailDto(
            fullDescription: $fullDescription,
            technologies: $technologies,
            salaryFrom: $salary['from'],
            salaryTo: $salary['to'],
            salaryCurrency: $salary['currency'],
            companyName: $companyName,
            contactEmail: $contactEmail,
            workType: $workType,
            seniority: $seniority,
        );
    }

    private function extractCompanyName(string $html, string $jobUrl): string
    {
        // Try to find company name near the job listing
        preg_match('/<a[^>]*href="'.preg_quote($jobUrl, '/').'"[^>]*>.*?<\/a>\s*<[^>]*>\s*<a[^>]*class="[^"]*company[^"]*"[^>]*>([^<]+)</si', $html, $match);

        if (! empty($match[1])) {
            return trim(strip_tags($match[1]));
        }

        return 'Unknown';
    }

    private function extractCompanyNameFromDetail(string $html): ?string
    {
        preg_match('/<span[^>]*class="[^"]*company[^"]*"[^>]*>([^<]+)</si', $html, $match);

        return trim($match[1] ?? '');
    }

    public function extractTechnologies(string $text): array
    {
        $knownTechs = [
            'PHP', 'Laravel', 'React', 'Vue.js', 'Angular', 'Node.js', 'Python',
            'JavaScript', 'TypeScript', 'Docker', 'Kubernetes', 'Redis', 'MySQL',
            'PostgreSQL', 'MongoDB', 'WordPress', 'Drupal', 'Symfony', 'CakePHP',
            'Yii', 'CodeIgniter', 'jQuery', 'Bootstrap', 'TailwindCSS', 'Sass',
            'LESS', 'Webpack', 'Vite', 'Git', 'Linux', 'Nginx', 'Apache',
            'RabbitMQ', 'Elasticsearch', 'Memcached', 'AWS', 'GCP', 'Azure',
            'CI/CD', 'Jenkins', 'GitHub Actions', 'GitLab CI', 'REST API',
            'GraphQL', 'WebSocket', 'HTML', 'CSS', 'XML', 'JSON', 'AJAX',
            'Livewire', 'Alpine.js', 'Filament', 'October CMS', 'Statamic',
            'MariaDB', 'SQLite', 'SQL Server', 'Oracle', 'Sphinx', 'Apache Solr',
            'TDD', 'BDD', 'PHPUnit', 'Pest', 'Selenium', 'Cypress',
        ];

        $found = [];

        foreach ($knownTechs as $tech) {
            if (stripos($text, $tech) !== false) {
                $found[] = $tech;
            }
        }

        return array_unique($found);
    }

    private function extractSalary(string $html): array
    {
        $patterns = [
            '/(\d[\d\s]*)\s*[-–]\s*(\d[\d\s]*)\s*(EUR|USD|MDL|€|\$|lei)/i',
            '/salary[^:]*:\s*(\d[\d\s]*)\s*[-–]\s*(\d[\d\s]*)\s*(EUR|USD|MDL|€|\$|lei)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $m)) {
                return [
                    'from' => (int) str_replace(' ', '', $m[1]),
                    'to' => (int) str_replace(' ', '', $m[2]),
                    'currency' => $this->normalizeCurrency($m[3]),
                ];
            }
        }

        return ['from' => null, 'to' => null, 'currency' => null];
    }

    private function normalizeCurrency(string $currency): string
    {
        return match ($currency) {
            '€' => 'EUR',
            '$' => 'USD',
            'lei' => 'MDL',
            default => strtoupper($currency),
        };
    }

    private function extractEmail(string $text): ?string
    {
        preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text, $match);

        return $match[0] ?? null;
    }

    private function detectWorkType(string $text): ?string
    {
        if (preg_match('/\b(remote|la distanță|distanță|online|de la distanță)\b/ui', $text)) {
            return 'remote';
        }

        if (preg_match('/\b(hybrid|hibrid)\b/ui', $text)) {
            return 'hybrid';
        }

        if (preg_match('/\b(office|birou|la birou|în birou)\b/ui', $text)) {
            return 'office';
        }

        return null;
    }

    private function detectSeniority(string $text): ?string
    {
        if (preg_match('/\b(senior|senior)\b/ui', $text)) {
            return 'senior';
        }

        if (preg_match('/\b(middle|mid)\b/ui', $text)) {
            return 'middle';
        }

        if (preg_match('/\b(junior|junior)\b/ui', $text)) {
            return 'junior';
        }

        if (preg_match('/\b(lead|team lead|tech lead)\b/ui', $text)) {
            return 'lead';
        }

        return null;
    }

    private function extractBetween(string $html, string $start, string $end): ?string
    {
        $startPos = strpos($html, $start);

        if ($startPos === false) {
            return null;
        }

        $endPos = strpos($html, $end, $startPos + strlen($start));

        if ($endPos === false) {
            return null;
        }

        $content = substr($html, $startPos, $endPos - $startPos);
        $content = strpos($content, '>') !== false ? substr($content, strpos($content, '>') + 1) : $content;

        return trim(strip_tags($content));
    }
}
