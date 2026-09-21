<?php

use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $page = Page::query()->where('slug', 'home')->first();

        if (! $page) {
            return;
        }

        $content = $page->content ?? [];
        $changed = false;

        $legacy = [
            'hy' => [
                'from' => 'Փորձ, որը վերածվել է հուսալի իրականացման։',
                'to' => 'Փորձից՝ հուսալի իրականացում։',
            ],
            'en' => [
                'from' => 'Experience turned into dependable implementation.',
                'to' => 'Experience, delivered reliably.',
            ],
        ];

        foreach ($legacy as $locale => $copy) {
            if (($content[$locale]['homeContent']['projectsTitle'] ?? null) === $copy['from']) {
                $content[$locale]['homeContent']['projectsTitle'] = $copy['to'];
                $changed = true;
            }
        }

        if ($changed) {
            $page->content = $content;
            $page->save();
        }
    }

    public function down(): void
    {
        // Intentionally left unchanged: admin-managed copy should not be reverted.
    }
};
