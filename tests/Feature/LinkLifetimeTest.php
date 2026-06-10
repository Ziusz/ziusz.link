<?php

namespace Tests\Feature;

use App\Enums\LinkLifetime;
use Illuminate\Support\Carbon;

test('link lifetime options expose admin labels', function () {
    expect(LinkLifetime::options())->toBe([
        '1_hour' => '1 hour',
        '12_hours' => '12 hours',
        '1_day' => '1 day',
        '3_days' => '3 days',
        '7_days' => '7 days',
        '14_days' => '14 days',
        '30_days' => '30 days',
        '3_months' => '3 months',
        '6_months' => '6 months',
        '1_year' => '1 year',
        'permanent' => 'Permanent',
    ]);
});

test('fourteen days is the default hidden link lifetime', function () {
    expect(LinkLifetime::default())->toBe(LinkLifetime::FourteenDays);
});

test('link lifetimes calculate expiration timestamps', function () {
    $from = Carbon::parse('2026-06-10 12:00:00');

    expect(LinkLifetime::OneHour->expiresAt($from)?->toDateTimeString())
        ->toBe('2026-06-10 13:00:00')
        ->and(LinkLifetime::TwelveHours->expiresAt($from)?->toDateTimeString())
        ->toBe('2026-06-11 00:00:00')
        ->and(LinkLifetime::OneDay->expiresAt($from)?->toDateTimeString())
        ->toBe('2026-06-11 12:00:00')
        ->and(LinkLifetime::ThreeDays->expiresAt($from)?->toDateTimeString())
        ->toBe('2026-06-13 12:00:00')
        ->and(LinkLifetime::SevenDays->expiresAt($from)?->toDateTimeString())
        ->toBe('2026-06-17 12:00:00')
        ->and(LinkLifetime::FourteenDays->expiresAt($from)?->toDateTimeString())
        ->toBe('2026-06-24 12:00:00')
        ->and(LinkLifetime::ThirtyDays->expiresAt($from)?->toDateTimeString())
        ->toBe('2026-07-10 12:00:00')
        ->and(LinkLifetime::ThreeMonths->expiresAt($from)?->toDateTimeString())
        ->toBe('2026-09-10 12:00:00')
        ->and(LinkLifetime::SixMonths->expiresAt($from)?->toDateTimeString())
        ->toBe('2026-12-10 12:00:00')
        ->and(LinkLifetime::OneYear->expiresAt($from)?->toDateTimeString())
        ->toBe('2027-06-10 12:00:00')
        ->and(LinkLifetime::Permanent->expiresAt($from))
        ->toBeNull();
});
