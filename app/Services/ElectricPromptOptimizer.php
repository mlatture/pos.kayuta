<?php

namespace App\Services;

use DB;
use Log;

use App\Models\Readings;
use App\Models\PromptTemplate;
use App\Models\SystemLog;

use Illuminate\Support\Str;

class ElectricPromptOptimizer
{
    protected $minExamples = 8;
    protected $passRateThreshold = 0.85;
    protected $improvementDelta = 0.03;

    public function run(int $minExamples = 8, bool $dryRun = false): array
    {
        $this->minExamples = max(1, $minExamples);

        $summary = [];
        $groups = $this->getFailedGroups();

        foreach ($groups as $groupKey => $groupMeta) {
            if ($count < $this->minExamples) {
                $summary[] = "skipping group {$groupKey} (count={$count} < min={$this->minExamples})";
                continue;
            }

            $examples = $this->getExamplesForGroup($groupMeta->manufacturer, $groupMeta->meter_style, 40);

            $summary[] = "Processing group {$groupKey} (manufacturer={$groupMeta->manufacturer}, style={$groupMeta->meter_style}, count={$count})";

            $currentPromptRow = PromptTemplate::where('type', 'meter_page')->first();
            $currentPrompt = $currentPromptRow->user_prompt ?? '';

            $candidatePrompt = $this->buildCandidatePrompt($currentPrompt, $groupMeta->manufacturer, $groupMeta->meter_style, $examples);

            $baselineMetrics = $this->evaluatePromptOnExamples($currentPrompt, $examples);
            $candidateMetrics = $this->evaluatePromptOnExamples($candidatePrompt, $examples);

            $summary[] = sprintf('Baseline pass rate: %.2f, Candidate pass rate: %.2f', $baselineMetrics['pass_rate'], $candidateMetrics['pass_rate']);

            $improved = $candidateMetrics['pass_rate'] >= $this->passRateThreshold && $candidateMetrics['pass_rate'] - $baselineMetrics['pass_rate'] >= $this->improvementDelta;

            if (!$improved) {
                SystemLog::create([
                    'transaction_type' => 'Training Failed',
                    'description' => "Prompt training failed for group manufacturer={$groupMeta->manufacturer}, style={$groupMeta->meter_style}, count={$count}",
                    'before' => json_encode([
                        'group' => ['manufacturer' => $groupMeta->manufacturer, 'meter_style' => $groupMeta->meter_style],
                        'count' => $count,
                        'baseline' => $baselineMetrics,
                        'candidate' => $candidateMetrics,
                        'note' => 'Candidate did not meet improvement threshold or pass rate',
                    ]),
                ]);
                $summary[] = 'Candidate prompt did not meet thresholds. Logged Training Failed.';
                continue;
            }

            if (!$dryRun) {
                DB::transaction(function () use ($candidatePrompt, $currentPromptRow, $groupMeta, $candidateMetrics) {
                    $version = 'optimized/' . now()->format('YmdHis') . '-' . Str::random(6);

                    $currentPromptRow->update([
                        'user_prompt' => $candidatePrompt,
                        'updated_at' => now(),
                    ]);

                    SystemLog::create([
                        'transaction_type' => 'Prompt Updated',
                        'description' => "Prompt updated to version {$version} for group manufacturer={$groupMeta->manufacturer}, style={$groupMeta->meter_style}",
                        'before' => json_encode([
                            'previous_prompt' => $currentPromptRow->user_prompt,
                            'new_prompt' => $candidatePrompt,
                            'group' => ['manufacturer' => $groupMeta->manufacturer, 'meter_style' => $groupMeta->meter_style],
                            'metrics' => $candidateMetrics,
                        ]),
                        'payload' => json_encode([
                            'version' => $version,
                            'type' => 'meter_page',
                            'group' => ['manufacturer' => $groupMeta->manufacturer, 'meter_style' => $groupMeta->meter_style],
                            'metrics' => $candidateMetrics,
                        ]),
                    ]);
                });

                $summary[] = "Candidate accepted and prompt_templates updated (version: {$version}).";
            } else {
                $summary[] = "Dry-run: candidate would have been accepted (pass_rate={$candidateMetrics['pass_rate']}).";
            }
        }

        return $summary;
    }

    protected function getFailedGroups()
    {
        return DB::table('electric_readings')
            ->select('manufacturer', 'meter_style', DB::raw('count(*) as cnt'))
            ->where('ai_success', 'false')
            ->groupBy('manufacturer', 'meter_style')
            ->orderByDesc('cnt')
            ->get()
            ->mapWithKeys(function ($r) {
                $k = trim($r->manufacturer ?? 'unknown') . '|' . trim($r->meter_style ?? 'unknown');
                return [$k => $r];
            });
    }

    protected function getExamplesForGroup($manufacturer, $meterStyle, $limit = 40)
    {
        return DB::table('electric_readings')
            ->where('ai_success', false)
            ->where(function ($q) use ($manufacturer, $meterStyle) {
                if (!empty($manufacturer)) {
                    $q->where('manufacturer', $manufacturer);
                }
                if (!empty($meterStyle)) {
                    $q->where('meter_style', $meterStyle);
                }
            })
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    protected function buildCandidatePrompt(string $basePrompt, $manufacturer, $meterStyle, array $examples): string
    {
        $extra = "\n\nIMPORTANT: For meters of manufacturer '" . ($manufacturer ?: 'unknown') . "' and style '" . ($meterStyle ?: 'unknown') . "', ";
        $extra .= "prioritize reading digits with this format: digits only, ignore labels like 'kWh' and obvious odometer artifacts. ";
        $extra .= "If meter numbers are printed on a white sticker below the dial, read from that sticker. Be conservative on low-contrast digits.\n";
        return trim($basePrompt . "\n\n" . $extra);
    }

    protected function evaluatePromptOnExamples(string $prompt, array $examples): array
    {
        $total = count($examples);
        $passes = 0;

        foreach ($examples as $ex) {
            $pass = $this->heuristicPass($prompt, $ex);
            if ($pass) {
                $passes++;
            }
        }

        $passRate = $total > 0 ? $passes / $total : 0.0;

        return [
            'total' => $total,
            'passes' => $passes,
            'pass_rate' => round($passRate, 4),
        ];
    }

    protected function heuristicPass(string $prompt, $example): bool
    {
        if (!empty($example->ai_meter_number)) {
            if (empty($example->ai_notes) || stripos($example->ai_notes, 'failed') === false) {
                return true;
            }
        }
        return false;
    }
}
