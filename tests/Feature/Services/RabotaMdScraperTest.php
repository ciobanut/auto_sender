<?php

use App\Services\RabotaMdScraper;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

test('fetches job details successfully with all fields mapped', function () {
    Http::fake([
        '*' => Http::response([
            'data' => [
                'company_ad' => [
                    'description_ad' => '<p><strong>PHP developer</strong> needed for web development.</p>',
                    'requirements' => "PHP 8+\nLaravel\nMySQL\nGit",
                    'company_name' => 'Tech Corp',
                    'salary_from' => 1000,
                    'salary_up_to' => 3000,
                    'currency' => 'EUR',
                    'ad_email' => ['hr@techcorp.md'],
                    'ad_phone' => ['+37360000000'],
                    'raised_at' => '2026-06-04 11:07:02',
                ],
                'workplace_formatted' => 'La distanță',
            ],
        ]),
    ]);

    $scraper = new RabotaMdScraper;
    $dto = $scraper->fetchJobDetails('https://www.rabota.md/ro/vacancy/test/99999');

    expect($dto)->not->toBeNull();
    expect($dto->fullDescription)->toBe('<p><strong>PHP developer</strong> needed for web development.</p> PHP 8+
Laravel
MySQL
Git');
    expect($dto->companyName)->toBe('Tech Corp');
    expect($dto->salaryFrom)->toBe(1000);
    expect($dto->salaryTo)->toBe(3000);
    expect($dto->salaryCurrency)->toBe('EUR');
    expect($dto->contactEmail)->toBe('hr@techcorp.md');
    expect($dto->phone)->toBe('+37360000000');
    expect($dto->workType)->toBe('remote');
    expect($dto->publicationDate)->toBe('2026-06-04 11:07:02');
});

test('returns null on http failure', function () {
    Http::fake([
        '*' => Http::response(null, 404),
    ]);

    $scraper = new RabotaMdScraper;
    $dto = $scraper->fetchJobDetails('https://www.rabota.md/ro/vacancy/test/99999');

    expect($dto)->toBeNull();
});

test('returns null on non-json body', function () {
    Http::fake([
        '*' => Http::response('not json', 200),
    ]);

    $scraper = new RabotaMdScraper;
    $dto = $scraper->fetchJobDetails('https://www.rabota.md/ro/vacancy/test/99999');

    expect($dto)->toBeNull();
});

test('returns null on missing data key', function () {
    Http::fake([
        '*' => Http::response(['wrong' => []], 200),
    ]);

    $scraper = new RabotaMdScraper;
    $dto = $scraper->fetchJobDetails('https://www.rabota.md/ro/vacancy/test/99999');

    expect($dto)->toBeNull();
});

test('returns null on empty description', function () {
    Http::fake([
        '*' => Http::response([
            'data' => [
                'company_ad' => [
                    'description_ad' => '<p></p>',
                ],
            ],
        ], 200),
    ]);

    $scraper = new RabotaMdScraper;
    $dto = $scraper->fetchJobDetails('https://www.rabota.md/ro/vacancy/test/99999');

    expect($dto)->toBeNull();
});

test('handles non-branded page with ad key instead of company_ad', function () {
    Http::fake([
        '*' => Http::response([
            'data' => [
                'ad' => [
                    'description_ad' => '<p>Non-branded job description text here.</p>',
                    'requirements' => 'Skill A',
                    'company_name' => 'Small Co',
                    'salary_from' => 500,
                    'salary_up_to' => 1500,
                    'currency' => 'MDL',
                ],
                'workplace_formatted' => 'În locația angajatorului',
            ],
        ], 200),
    ]);

    $scraper = new RabotaMdScraper;
    $dto = $scraper->fetchJobDetails('https://www.rabota.md/ro/vacancy/test/99999');

    expect($dto)->not->toBeNull();
    expect($dto->companyName)->toBe('Small Co');
    expect($dto->salaryFrom)->toBe(500);
    expect($dto->salaryTo)->toBe(1500);
    expect($dto->salaryCurrency)->toBe('MDL');
    expect($dto->workType)->toBe('office');
});

test('parses requirements into array by newlines', function () {
    Http::fake([
        '*' => Http::response([
            'data' => [
                'company_ad' => [
                    'description_ad' => '<p>We need someone skilled.</p>',
                    'requirements' => "PHP 8+\nLaravel\nMySQL",
                ],
            ],
        ], 200),
    ]);

    $scraper = new RabotaMdScraper;
    $dto = $scraper->fetchJobDetails('https://www.rabota.md/ro/vacancy/test/99999');

    expect($dto)->not->toBeNull();
    expect($dto->requirements)->toBe(['PHP 8+', 'Laravel', 'MySQL']);
});

test('returns null requirements for null input', function () {
    Http::fake([
        '*' => Http::response([
            'data' => [
                'company_ad' => [
                    'description_ad' => '<p>We need someone skilled.</p>',
                    'requirements' => null,
                ],
            ],
        ], 200),
    ]);

    $scraper = new RabotaMdScraper;
    $dto = $scraper->fetchJobDetails('https://www.rabota.md/ro/vacancy/test/99999');

    expect($dto)->not->toBeNull();
    expect($dto->requirements)->toBeNull();
});

test('returns null requirements for empty string', function () {
    Http::fake([
        '*' => Http::response([
            'data' => [
                'company_ad' => [
                    'description_ad' => '<p>We need someone skilled.</p>',
                    'requirements' => '',
                ],
            ],
        ], 200),
    ]);

    $scraper = new RabotaMdScraper;
    $dto = $scraper->fetchJobDetails('https://www.rabota.md/ro/vacancy/test/99999');

    expect($dto)->not->toBeNull();
    expect($dto->requirements)->toBeNull();
});

test('maps workplace formatted to work type for all variants', function (string $workplace, string $expected) {
    Http::fake([
        '*' => Http::response([
            'data' => [
                'company_ad' => [
                    'description_ad' => '<p>Job description text here for testing purposes.</p>',
                ],
                'workplace_formatted' => $workplace,
            ],
        ], 200),
    ]);

    $scraper = new RabotaMdScraper;
    $dto = $scraper->fetchJobDetails('https://www.rabota.md/ro/vacancy/test/99999');

    expect($dto)->not->toBeNull();
    expect($dto->workType)->toBe($expected);
})->with([
    ['La distanță', 'remote'],
    ['Remote', 'remote'],
    ['De la distanță', 'remote'],
    ['Online', 'remote'],
    ['Hibrid', 'hybrid'],
    ['Hybrid', 'hybrid'],
    ['În locația angajatorului', 'office'],
    ['La birou', 'office'],
    ['Birou', 'office'],
]);

test('handles zero salary as null', function () {
    Http::fake([
        '*' => Http::response([
            'data' => [
                'company_ad' => [
                    'description_ad' => '<p>Job description with enough text for testing.</p>',
                    'salary_from' => 0,
                    'salary_up_to' => 0,
                ],
            ],
        ], 200),
    ]);

    $scraper = new RabotaMdScraper;
    $dto = $scraper->fetchJobDetails('https://www.rabota.md/ro/vacancy/test/99999');

    expect($dto)->not->toBeNull();
    expect($dto->salaryFrom)->toBeNull();
    expect($dto->salaryTo)->toBeNull();
});

test('handles positive salary values', function () {
    Http::fake([
        '*' => Http::response([
            'data' => [
                'company_ad' => [
                    'description_ad' => '<p>Job description with enough text for testing.</p>',
                    'salary_from' => 500,
                    'salary_up_to' => 1500,
                    'currency' => 'EUR',
                ],
            ],
        ], 200),
    ]);

    $scraper = new RabotaMdScraper;
    $dto = $scraper->fetchJobDetails('https://www.rabota.md/ro/vacancy/test/99999');

    expect($dto)->not->toBeNull();
    expect($dto->salaryFrom)->toBe(500);
    expect($dto->salaryTo)->toBe(1500);
    expect($dto->salaryCurrency)->toBe('EUR');
});

test('extracts technologies from description', function () {
    Http::fake([
        '*' => Http::response([
            'data' => [
                'company_ad' => [
                    'description_ad' => '<p>Need Laravel, Vue.js and Docker skills.</p>',
                ],
            ],
        ], 200),
    ]);

    $scraper = new RabotaMdScraper;
    $dto = $scraper->fetchJobDetails('https://www.rabota.md/ro/vacancy/test/99999');

    expect($dto)->not->toBeNull();
    expect($dto->technologies)->toContain('Laravel');
    expect($dto->technologies)->toContain('Vue.js');
    expect($dto->technologies)->toContain('Docker');
});

test('detects seniority from description', function () {
    Http::fake([
        '*' => Http::response([
            'data' => [
                'company_ad' => [
                    'description_ad' => '<p>Looking for a senior PHP developer.</p>',
                ],
            ],
        ], 200),
    ]);

    $scraper = new RabotaMdScraper;
    $dto = $scraper->fetchJobDetails('https://www.rabota.md/ro/vacancy/test/99999');

    expect($dto)->not->toBeNull();
    expect($dto->seniority)->toBe('senior');
});

test('handles contact email as array', function () {
    Http::fake([
        '*' => Http::response([
            'data' => [
                'company_ad' => [
                    'description_ad' => '<p>Job description with enough text for testing.</p>',
                    'ad_email' => ['first@test.com', 'second@test.com'],
                ],
            ],
        ], 200),
    ]);

    $scraper = new RabotaMdScraper;
    $dto = $scraper->fetchJobDetails('https://www.rabota.md/ro/vacancy/test/99999');

    expect($dto)->not->toBeNull();
    expect($dto->contactEmail)->toBe('first@test.com');
});

test('handles contact phone as array', function () {
    Http::fake([
        '*' => Http::response([
            'data' => [
                'company_ad' => [
                    'description_ad' => '<p>Job description with enough text for testing.</p>',
                    'ad_phone' => ['+37360000000'],
                ],
            ],
        ], 200),
    ]);

    $scraper = new RabotaMdScraper;
    $dto = $scraper->fetchJobDetails('https://www.rabota.md/ro/vacancy/test/99999');

    expect($dto)->not->toBeNull();
    expect($dto->phone)->toBe('+37360000000');
});

test('strips existing query params before appending json params', function () {
    Http::fake();

    $scraper = new RabotaMdScraper;
    $scraper->fetchJobDetails('https://www.rabota.md/ro/vacancy/test/99999?some=param');

    Http::assertSent(function (Request $request) {
        return str_contains($request->url(), 'json=1')
            && ! str_contains($request->url(), 'some=param');
    });
});
