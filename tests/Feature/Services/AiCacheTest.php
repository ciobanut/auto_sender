<?php

use App\Models\JobDetail;
use App\Services\AiCache;

beforeEach(function () {
    Cache::flush();
});

test('generates consistent cache key for same inputs', function () {
    $detail = JobDetail::factory()->create();
    $detail2 = JobDetail::find($detail->id);

    $key1 = AiCache::key($detail, 'cv text content', 'instructions here', false);
    $key2 = AiCache::key($detail2, 'cv text content', 'instructions here', false);

    expect($key1)->toBe($key2);
});

test('generates different cache key for different inputs', function () {
    $detail = JobDetail::factory()->create();

    $key1 = AiCache::key($detail, 'cv content A', 'instructions', false);
    $key2 = AiCache::key($detail, 'cv content B', 'instructions', false);
    $key3 = AiCache::key($detail, 'cv content A', 'different instructions', false);

    // Different follow-up flag should produce different key
    $key4 = AiCache::key($detail, 'cv content A', 'instructions', true);

    expect($key1)->not->toBe($key2);
    expect($key1)->not->toBe($key3);
    expect($key1)->not->toBe($key4);
});

test('stores and retrieves cached results', function () {
    $detail = JobDetail::factory()->create();
    $key = AiCache::key($detail, 'cv', 'instr', false);

    $result = AiCache::get($key);
    expect($result)->toBeNull();

    $data = ['cover_letter' => 'Test letter', 'confidence_score' => 0.95, 'match_reasons' => []];
    AiCache::set($key, $data);

    $cached = AiCache::get($key);
    expect($cached)->toBe($data);
});

test('follow-up flag produces different cache key', function () {
    $detail = JobDetail::factory()->create();

    $initialKey = AiCache::key($detail, 'cv', 'instr', false);
    $followUpKey = AiCache::key($detail, 'cv', 'instr', true);

    expect($initialKey)->not->toBe($followUpKey);
});
