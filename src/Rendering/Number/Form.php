<?php
declare(strict_types=1);
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2020 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Number;

use MyCLabs\Enum\Enum;

class Form extends Enum
{

    public const NUMERIC = "numeric";

    public const ORDINAL = "ordinal";

    public const LONG_ORDINAL = "long-ordinal";

    public const ROMAN = "roman";
}
