<?php

namespace App\Fixtures;

use mysqli;

interface FixtureInterface
{
    public static function load(): void;
}