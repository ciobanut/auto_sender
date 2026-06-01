<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;

#[Provider(Lab::DeepSeek)]
#[Model('deepseek-v4-flash')]
#[MaxTokens(1024)]
#[Temperature(0.7)]
class FollowUpWriterAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function __construct(
        public string $dynamicInstructions = '',
        public ?float $dynamicTemperature = null,
        public ?int $dynamicMaxTokens = null,
    ) {}

    public function instructions(): string
    {
        return $this->dynamicInstructions;
    }

    public function temperature(): ?float
    {
        return $this->dynamicTemperature;
    }

    public function maxTokens(): ?int
    {
        return $this->dynamicMaxTokens;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'cover_letter' => $schema->string()->required(),
            'confidence_score' => $schema->number()->required(),
            'new_skills_highlighted' => $schema->array($schema->string())->required(),
            'match_reasons' => $schema->array($schema->string())->required(),
        ];
    }
}
