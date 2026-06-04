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
        // Prefix with meta charset to ensure UTF-8 encoding
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

            // --- Title ---
            $title = '';
            $titleNodes = $xpath->query(".//span[contains(@class, 'sm:line-clamp-1')]", $card);
            if ($titleNodes->length > 0) {
                $title = trim($titleNodes->item(0)->textContent);
            }

            if (empty($title) || strlen($title) < 3) {
                continue;
            }

            // --- Job URL ---
            $relativeUrl = '';
            $linkNodes = $xpath->query(".//a[contains(@class, 'vacancyShowPopup')]", $card);
            if ($linkNodes->length > 0) {
                $relativeUrl = $linkNodes->item(0)->getAttribute('href');
            }
            $fullUrl = $relativeUrl ? self::BASE_URL.$relativeUrl : '';

            // --- Company name ---
            $companyName = 'Unknown';
            // Try logo alt first (companies with a logo image)
            $imgNodes = $xpath->query(".//img[contains(@class, 'mx-auto')]", $card);
            if ($imgNodes->length > 0 && $imgNodes->item(0)->hasAttribute('alt')) {
                $alt = trim($imgNodes->item(0)->getAttribute('alt'));
                if ($alt !== '') {
                    $companyName = $alt;
                }
            }

            // --- Info bar (company, location, salary spans) ---
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

            // Fallback: first info bar span (cards without company logo/link)
            if ($companyName === 'Unknown' && isset($infoSpanTexts[0])) {
                $companyName = $infoSpanTexts[0];
            }

            // --- Location ---
            $location = 'Moldova';
            if (isset($infoSpanTexts[1])) {
                $candidate = $infoSpanTexts[1];
                // Skip if it looks like a salary value
                if (! preg_match('/[\d]/', $candidate)) {
                    $location = $candidate;
                }
            }

            // --- Short preview ---
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

    private function parseJobDetail(string $html): ?JobDetailDto
    {
        $fullDescription = $this->extractDescription($html);

        if (! $fullDescription) {
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

    private function extractDescription(string $html): ?string
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument;
        $dom->loadHTML('<meta charset="utf-8">'.$html);
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);

        // Find the vacancy-content div (main job description)
        $nodes = $xpath->query("//div[contains(@class, 'vacancy-content')]");

        if ($nodes->length === 0) {
            // Fallback: clean the entire page
            $text = $this->cleanHtmlText($html);

            return strlen($text) >= 20 ? $text : null;
        }

        // Get inner HTML of the vacancy-content div
        $inner = '';
        foreach ($nodes->item(0)->childNodes as $child) {
            $inner .= $dom->saveHTML($child);
        }

        $text = $this->cleanHtmlText($inner);

        return strlen($text) >= 20 ? $text : null;
    }

    private function cleanHtmlText(string $html): string
    {
        $text = preg_replace('/<script\b[^>]*>.*?<\/script>/si', '', $html);
        $text = preg_replace('/<style\b[^>]*>.*?<\/style>/si', '', $text);
        $text = preg_replace('/<!--.*?-->/s', '', $text);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/', ' ', $text));
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
}
