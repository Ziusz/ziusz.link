<?php

namespace App\Enums;

use Carbon\CarbonInterface;

enum LinkLifetime: string
{
    case OneHour = '1_hour';
    case TwelveHours = '12_hours';
    case OneDay = '1_day';
    case ThreeDays = '3_days';
    case SevenDays = '7_days';
    case FourteenDays = '14_days';
    case ThirtyDays = '30_days';
    case ThreeMonths = '3_months';
    case SixMonths = '6_months';
    case OneYear = '1_year';
    case Permanent = 'permanent';

    public static function default(): self
    {
        return self::FourteenDays;
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $lifetime): array => [$lifetime->value => $lifetime->label()])
            ->all();
    }

    public function label(): string
    {
        return match ($this) {
            self::OneHour => '1 hour',
            self::TwelveHours => '12 hours',
            self::OneDay => '1 day',
            self::ThreeDays => '3 days',
            self::SevenDays => '7 days',
            self::FourteenDays => '14 days',
            self::ThirtyDays => '30 days',
            self::ThreeMonths => '3 months',
            self::SixMonths => '6 months',
            self::OneYear => '1 year',
            self::Permanent => 'Permanent',
        };
    }

    public function expiresAt(?CarbonInterface $from = null): ?CarbonInterface
    {
        $from = ($from ?? now())->copy();

        return match ($this) {
            self::OneHour => $from->addHour(),
            self::TwelveHours => $from->addHours(12),
            self::OneDay => $from->addDay(),
            self::ThreeDays => $from->addDays(3),
            self::SevenDays => $from->addDays(7),
            self::FourteenDays => $from->addDays(14),
            self::ThirtyDays => $from->addDays(30),
            self::ThreeMonths => $from->addMonths(3),
            self::SixMonths => $from->addMonths(6),
            self::OneYear => $from->addYear(),
            self::Permanent => null,
        };
    }
}
