<?php

declare(strict_types=1);

namespace Andi\GraphQL\WebonyxType;

use Andi\GraphQL\InputObjectFieldResolver\InputObjectFieldResolverInterface;
use Andi\GraphQL\Type\DynamicObjectTypeInterface;
use GraphQL\Type\Definition as Webonyx;

/**
 * @see Webonyx\InputObjectType
 *
 * @phpstan-import-type InputObjectConfig from Webonyx\InputObjectType
 */
class InputObjectType extends Webonyx\InputObjectType implements DynamicObjectTypeInterface
{
    /**
     * @var callable|iterable
     */
    private readonly mixed $nativeFields;

    private array $additionalFields = [];

    /**
     * @param InputObjectConfig $config
     * @param InputObjectFieldResolverInterface $inputObjectFieldResolver
     */
    public function __construct(
        array $config,
        private readonly InputObjectFieldResolverInterface $inputObjectFieldResolver,
    ) {
        if (isset($config['fields'])) {
            $this->nativeFields = $config['fields'];
        }

        $config['fields'] = $this->extractFields(...);

        parent::__construct($config);
    }

    public function addAdditionalField(mixed $field): static
    {
        $this->additionalFields[] = $field;

        return $this;
    }

    private function extractFields(): iterable
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (isset($this->nativeFields)) {
            $fields = \is_callable($this->nativeFields)
                ? \call_user_func($this->nativeFields)
                : $this->nativeFields;

            if (\is_iterable($fields)) {
                foreach ($fields as $field) {
                    yield $this->inputObjectFieldResolver->resolve($field);
                }
            }
        }

        foreach ($this->additionalFields as $field) {
            yield $this->inputObjectFieldResolver->resolve($field);
        }
    }
}
