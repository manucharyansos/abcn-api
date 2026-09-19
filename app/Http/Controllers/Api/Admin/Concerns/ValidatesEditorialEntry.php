<?php

namespace App\Http\Controllers\Api\Admin\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

trait ValidatesEditorialEntry
{
    protected function editorialRules(string $table, ?Model $entry = null, array $extra = []): array
    {
        return [
            'slug' => ['required', 'alpha_dash', 'max:160', Rule::unique($table)->ignore($entry)],
            'status' => ['required', 'in:draft,published,archived'],
            'show_on_homepage' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'translations' => ['required', 'array'],
            'translations.hy.title' => ['required', 'string', 'max:220'],
            'translations.hy.summary' => ['nullable', 'string', 'max:1500'],
            'translations.hy.body' => ['nullable', 'string', 'max:30000'],
            'translations.en.title' => ['required', 'string', 'max:220'],
            'translations.en.summary' => ['nullable', 'string', 'max:1500'],
            'translations.en.body' => ['nullable', 'string', 'max:30000'],
            'images' => ['nullable', 'array', 'max:4'],
            'images.*.url' => ['required', 'string', 'max:2048'],
            'images.*.name' => ['nullable', 'string', 'max:255'],
            'images.*.alt' => ['nullable', 'array'],
            'images.*.alt.hy' => ['nullable', 'string', 'max:500'],
            'images.*.alt.en' => ['nullable', 'string', 'max:500'],
            ...$extra,
        ];
    }
}
