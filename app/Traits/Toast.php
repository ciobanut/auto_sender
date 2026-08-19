<?php

namespace App\Traits;

trait Toast
{
    /**
     * Dispatch a toast notification via BlatUI Sonner.
     */
    public function toast(
        string $title,
        ?string $description = null,
        string $type = 'default',
        int $duration = 4000,
        ?string $redirectTo = null,
    ): void {
        $payload = [
            'title' => $title,
            'type' => $type,
            'duration' => $duration,
        ];

        if ($description) {
            $payload['description'] = $description;
        }

        $this->js('window.toast('.json_encode($payload).')');

        if ($redirectTo) {
            $this->redirect($redirectTo, navigate: true);
        }
    }

    public function success(
        string $title,
        ?string $description = null,
        int $duration = 4000,
        ?string $redirectTo = null,
    ): void {
        $this->toast($title, $description, 'success', $duration, $redirectTo);
    }

    public function warning(
        string $title,
        ?string $description = null,
        int $duration = 4000,
        ?string $redirectTo = null,
    ): void {
        $this->toast($title, $description, 'warning', $duration, $redirectTo);
    }

    public function error(
        string $title,
        ?string $description = null,
        int $duration = 4000,
        ?string $redirectTo = null,
    ): void {
        $this->toast($title, $description, 'error', $duration, $redirectTo);
    }

    public function info(
        string $title,
        ?string $description = null,
        int $duration = 4000,
        ?string $redirectTo = null,
    ): void {
        $this->toast($title, $description, 'info', $duration, $redirectTo);
    }
}
