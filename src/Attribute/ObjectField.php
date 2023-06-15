<?php

declare(strict_types=1);

namespace Andi\GraphQL\Attribute;

use Attribute;
use Spiral\Attributes\NamedArgumentConstructor;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PROPERTY), NamedArgumentConstructor]
final class ObjectField extends AbstractDefinition
{
    public function __construct(
        ?string $name = null,
        ?string $description = null,
        public readonly ?string $type = null,
        public readonly ?string $deprecationReason = null,
    ) {
        parent::__construct($name, $description);
    }
}
