<?php
/*
 * Copyright (c) 2025. All Rights Reserved.
 *
 * This file is part of the OpenCal project, see https://git.var-lab.com/opencal
 *
 * You may use, distribute and modify this code under the terms of the AGPL 3.0 license,
 * which unfortunately won't be written for another century.
 *
 * Visit https://git.var-lab.com/opencal/backend/-/blob/main/LICENSE to read the full license text.
 */

declare(strict_types=1);

namespace App\Tests\UnitTests\Helper;

use App\Helper\SlugHelper;
use PHPUnit\Framework\TestCase;

class SlugHelperTest extends TestCase
{
    public function testSlugifyGerman(): void
    {
        $helper = new SlugHelper('de_DE');
        $slug   = $helper->slugify('Das ist großer schöner See!');

        self::assertSame(
            'das-ist-grosser-schoener-see',
            $slug,
        );
    }

    public function testSlugifyEnglish(): void
    {
        $helper = new SlugHelper('en_GB');
        $slug   = $helper->slugify('That\'s a big, beautiful lake!');

        self::assertSame(
            'that-s-a-big-beautiful-lake',
            $slug,
        );
    }
}
