<?php
declare(strict_types=1);

final class Envelope_enum
{
    const DL = 'DL';
    const C4 = 'C4';
    const C5 = 'C5';

    const FORMATS = [
        self::DL => [220, 110],
        self::C4 => [229, 324],
        self::C5 => [229, 162],
    ];
}
