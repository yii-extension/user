<?php

declare(strict_types=1);

namespace Yii\Extension\User\Helper;

use DateTime;
use DateTimeZone;
use Yiisoft\Arrays\ArraySorter;

final class TimeZone
{
    public function getAll(): array
    {
        $timeZones = [];
        $timeZoneIdentifiers = DateTimeZone::listIdentifiers();

        foreach ($timeZoneIdentifiers as $timeZone) {
            $date = new DateTime('now', new DateTimeZone($timeZone));
            $offset = $date->getOffset();

            if ($offset > 0) {
                $tz = '+' . gmdate('H:i', (int) abs($offset));
            } else {
                $tz = '-' . gmdate('H:i', (int) abs($offset));
            }

            $timeZones[] = ['timezone' => $timeZone, 'name' => "{$timeZone} (UTC {$tz})", 'offset' => $offset];
        }

        ArraySorter::multisort($timeZones, 'offset', SORT_DESC, SORT_NUMERIC);

        return $timeZones;
    }
}
