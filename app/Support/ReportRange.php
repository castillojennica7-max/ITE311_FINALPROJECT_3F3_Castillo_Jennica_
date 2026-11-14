<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

// Saklaw ng petsa para sa mga report (default: huling 30 araw)
class ReportRange
{
    public function __construct(public Carbon $from, public Carbon $to)
    {
    }

    public static function fromRequest(Request $request): self
    {
        $parse = function (?string $value): ?Carbon {
            if (! $value) {
                return null;
            }

            try {
                return Carbon::createFromFormat('Y-m-d', $value);
            } catch (\Throwable) {
                return null;
            }
        };

        $to   = $parse($request->query('to'))?->endOfDay() ?? now()->endOfDay();
        $from = $parse($request->query('from'))?->startOfDay() ?? $to->copy()->subDays(29)->startOfDay();

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        // Hanggang isang taon lang bawat report
        if ($from->copy()->startOfDay()->diffInDays($to->copy()->startOfDay()) > 365) {
            $from = $to->copy()->subDays(365)->startOfDay();
        }

        return new self($from, $to);
    }

    public function between(): array
    {
        return [$this->from, $this->to];
    }

    public function days(): int
    {
        return (int) $this->from->copy()->startOfDay()->diffInDays($this->to->copy()->startOfDay()) + 1;
    }

    public function params(): array
    {
        return ['from' => $this->from->toDateString(), 'to' => $this->to->toDateString()];
    }

    public function label(): string
    {
        return $this->from->format('M d, Y') . ' to ' . $this->to->format('M d, Y');
    }

    // Isang entry bawat araw, para sa mga chart
    public function emptySeries(): array
    {
        $series = [];

        for ($d = $this->from->copy()->startOfDay(); $d->lessThanOrEqualTo($this->to); $d->addDay()) {
            $series[$d->toDateString()] = 0;
        }

        return $series;
    }
}
