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
        try {
            $cleanUrl = strtok($url, '?');
            $jsonUrl = $cleanUrl.'?json=1&viewMode=1';

            $response = Http::retry(3, 2000)
                ->timeout(15)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
                    'Accept' => 'application/json',
                    'Accept-Language' => 'ro-RO,ro;q=0.9,en;q=0.8',
                ])
                ->get($jsonUrl);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();

            if (! is_array($data) || empty($data['data'])) {
                return null;
            }

            return $this->parseJobDetailFromJson($data['data']);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseJobListings(string $html, string $keyword): Collection
    {
        $jobs = collect();
        $seen = [];

        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<meta charset="utf-8">'.$html);
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);

        $cards = $xpath->query('//div[@data-vacancyid]');

        foreach ($cards as $card) {
            if (count($seen) >= 20) {
                break;
            }

            $externalJobId = $card->getAttribute('data-vacancyid');

            if (isset($seen[$externalJobId])) {
                continue;
            }
            $seen[$externalJobId] = true;

            $title = '';
            $titleNodes = $xpath->query(".//span[contains(@class, 'sm:line-clamp-1')]", $card);
            if ($titleNodes->length > 0) {
                $title = trim($titleNodes->item(0)->textContent);
            }

            if (empty($title) || strlen($title) < 3) {
                continue;
            }

            $relativeUrl = '';
            $linkNodes = $xpath->query(".//a[contains(@class, 'vacancyShowPopup')]", $card);
            if ($linkNodes->length > 0) {
                $relativeUrl = $linkNodes->item(0)->getAttribute('href');
            }
            $fullUrl = $relativeUrl ? self::BASE_URL.$relativeUrl : '';

            $companyName = 'Unknown';
            $imgNodes = $xpath->query(".//img[contains(@class, 'mx-auto')]", $card);
            if ($imgNodes->length > 0 && $imgNodes->item(0)->hasAttribute('alt')) {
                $alt = trim($imgNodes->item(0)->getAttribute('alt'));
                if ($alt !== '') {
                    $companyName = $alt;
                }
            }

            $infoBar = $xpath->query(".//div[contains(@class, 'gap-x-6') and contains(@class, 'text-black')]", $card);
            $infoSpanTexts = [];
            if ($infoBar->length > 0) {
                $spans = $xpath->query('.//span', $infoBar->item(0));
                foreach ($spans as $span) {
                    $text = trim($span->textContent);
                    if ($text !== '') {
                        $infoSpanTexts[] = $text;
                    }
                }
            }

            if ($companyName === 'Unknown' && isset($infoSpanTexts[0])) {
                $companyName = $infoSpanTexts[0];
            }

            $location = 'Moldova';
            if (isset($infoSpanTexts[1])) {
                $candidate = $infoSpanTexts[1];
                if (! preg_match('/[\d]/', $candidate)) {
                    $location = $candidate;
                }
            }

            $shortPreview = null;
            $previewNodes = $xpath->query(".//p[contains(@class, 'line-clamp-3')]", $card);
            if ($previewNodes->length > 0) {
                $shortPreview = trim($previewNodes->item(0)->textContent);
                $shortPreview = trim(preg_replace('/\s+/', ' ', $shortPreview));
            }

            $jobs->push(new JobLinkDto(
                jobUrl: $fullUrl,
                title: $title,
                companyName: $companyName,
                location: $location,
                shortPreview: $shortPreview,
                externalJobId: $externalJobId,
            ));
        }

        return $jobs;
    }

    private function parseJobDetailFromJson(array $data): ?JobDetailDto
    {
        $companyAd = $data['company_ad'] ?? $data['ad'] ?? [];

        $adDescription = $companyAd['description_ad'] ?? $companyAd['description'] ?? '';
        $rawRequirements = $companyAd['requirements'] ?? '';

        // Combine both fields for the full description (some jobs put everything in requirements)
        $combined = trim($adDescription.' '.$rawRequirements);
        $fullDescription = $this->cleanApiDescription($combined);

        if ($fullDescription === null || strlen($fullDescription) < 20) {
            return null;
        }

        $technologies = $this->extractTechnologies($fullDescription);

        $salaryFrom = isset($companyAd['salary_from']) && $companyAd['salary_from'] > 0
            ? (int) $companyAd['salary_from'] : null;
        $salaryTo = isset($companyAd['salary_up_to']) && $companyAd['salary_up_to'] > 0
            ? (int) $companyAd['salary_up_to'] : null;
        $salaryCurrency = $companyAd['currency'] ?? null;

        $companyName = $companyAd['company_name'] ?? null;

        $emails = $companyAd['ad_email'] ?? [];
        $contactEmail = is_array($emails) ? ($emails[0] ?? null) : $emails;

        $phones = $companyAd['ad_phone'] ?? [];
        $phone = is_array($phones) ? ($phones[0] ?? null) : $phones;

        $requirements = $this->parseRequirementsToArray($rawRequirements);

        $publicationDate = $companyAd['raised_at'] ?? null;

        $workType = $this->mapWorkplaceFromApi($data['workplace_formatted'] ?? null)
            ?? $this->detectWorkType($fullDescription);

        $seniority = $this->detectSeniority($fullDescription);

        return new JobDetailDto(
            fullDescription: $fullDescription,
            technologies: $technologies,
            salaryFrom: $salaryFrom,
            salaryTo: $salaryTo,
            salaryCurrency: $salaryCurrency,
            companyName: $companyName,
            contactEmail: $contactEmail,
            phone: $phone,
            requirements: $requirements,
            seniority: $seniority,
            workType: $workType,
            publicationDate: $publicationDate,
        );
    }

    private function cleanApiDescription(string $html): ?string
    {
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/', ' ', $text)) ?: null;
    }

    private function parseRequirementsToArray(?string $requirements): ?array
    {
        if ($requirements === null || trim($requirements) === '') {
            return null;
        }

        // Convert block-level HTML tags to newlines before stripping tags
        $text = preg_replace('/<\/(?:p|li|tr|div|h[1-6])\s*>/i', "\n", $requirements);
        $text = preg_replace('/<br\s*\/?>/i', "\n", $text);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim(preg_replace('/\n\s+/', "\n", $text));

        $lines = explode("\n", $text);
        $lines = array_map('trim', $lines);
        $lines = array_values(array_filter($lines, fn (string $line) => $line !== ''));

        // If still one long line, try bullet-point delimiters
        if (count($lines) <= 1 && ! empty($lines[0])) {
            $lines = preg_split('/\s*[•\-\*]\s*/', $lines[0]);
            $lines = array_map('trim', $lines);
            $lines = array_values(array_filter($lines, fn (string $line) => $line !== ''));
        }

        return ! empty($lines) ? $lines : null;
    }

    private function mapWorkplaceFromApi(?string $workplaceFormatted): ?string
    {
        $workplace = $workplaceFormatted ?? '';

        if (preg_match('/\b(remote|la distanță|de la distanță|online)\b/ui', $workplace)) {
            return 'remote';
        }

        if (preg_match('/\b(hybrid|hibrid)\b/ui', $workplace)) {
            return 'hybrid';
        }

        if (preg_match('/\b(office|birou|la birou|în birou|în locația)\b/ui', $workplace)) {
            return 'office';
        }

        return null;
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
}
