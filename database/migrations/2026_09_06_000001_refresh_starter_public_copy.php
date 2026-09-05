<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $replacements = [
            'solutions' => [
                'hy' => [
                    'Յուրաքանչյուր ուղղություն կարող է ներառել կիրառման սցենարներ, համակարգային սխեմաներ, համատեղելի ապրանքներ և տեխնիկական փաստաթղթեր։',
                    'Յուրաքանչյուր ուղղություն ձևավորվում է կիրառության, շահագործման միջավայրի և ակնկալվող արդյունքի շուրջ՝ նախագծին համապատասխան սարքավորումներով ու տեխնիկական փաստաթղթերով։',
                ],
                'en' => [
                    'Each direction can include application scenarios, system diagrams, compatible products and technical documents.',
                    'Each direction is shaped around the application, operating environment and required result, with equipment and technical documents selected for the project.',
                ],
                'field' => 'body',
            ],
            'products' => [
                'hy' => [
                    'Ապրանքներն այստեղ հրապարակվում են ադմինկայից՝ հաստատված տեխնիկական տվյալներով։',
                    'Յուրաքանչյուր ապրանքի էջից հնարավոր կլինի հարցնել առկայության, ընտրության կամ կոմերցիոն առաջարկի մասին՝ առանց օնլայն վճարման։',
                ],
                'en' => [
                    'Products are published here from the administration panel with verified technical data.',
                    'Each product page will provide a direct way to ask about availability, selection support or a commercial offer without online payment.',
                ],
                'field' => 'body',
            ],
            'contact' => [
                'hy' => [
                    'Ուղարկեք ձեր հարցի կամ նախագծի կարճ նկարագրությունը։ Հարցումը կհայտնվի ABCN-ի ադմինկայում։',
                    'Ուղարկեք ձեր հարցի կամ նախագծի կարճ նկարագրությունը։ Մեր թիմը կուսումնասիրի այն և կկապվի ձեզ հետ՝ հաջորդ քայլը հստակեցնելու համար։',
                ],
                'en' => [
                    'Send a short description of your question or project. The inquiry will appear in the ABCN administration panel.',
                    'Send a short description of your question or project. Our team will review it and contact you to clarify the next step.',
                ],
                'field' => 'lead',
            ],
        ];

        foreach ($replacements as $slug => $replacement) {
            $page = DB::table('pages')->where('slug', $slug)->first();

            if (! $page) {
                continue;
            }

            $content = json_decode($page->content, true);
            $meta = json_decode($page->meta ?: '{}', true);

            if (! is_array($content)) {
                continue;
            }

            if (! is_array($meta)) {
                $meta = [];
            }

            $changed = false;

            foreach (['hy', 'en'] as $locale) {
                [$old, $new] = $replacement[$locale];
                $field = $replacement['field'];

                if (($content[$locale][$field] ?? null) === $old) {
                    $content[$locale][$field] = $new;
                    $changed = true;
                }

                if ($field === 'lead' && ($meta[$locale]['description'] ?? null) === $old) {
                    $meta[$locale]['description'] = $new;
                    $changed = true;
                }
            }

            if ($changed) {
                DB::table('pages')->where('id', $page->id)->update([
                    'content' => json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'meta' => json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Content migrations are intentionally not reversed to avoid overwriting later admin edits.
    }
};
