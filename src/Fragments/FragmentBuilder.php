<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Fragments;

use SushiDev\Fairu\Contracts\FragmentInterface;

class FragmentBuilder implements FragmentInterface
{
    private array $fields = [];

    private array $relations = [];

    private ?string $name = null;

    public function __construct(
        private readonly string $typeName,
    ) {}

    public static function for(string $typeName): self
    {
        return new self($typeName);
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function select(array $fields): self
    {
        $this->fields = array_merge($this->fields, $fields);

        return $this;
    }

    public function field(string $field): self
    {
        $this->fields[] = $field;

        return $this;
    }

    public function with(string $relation, callable|FragmentInterface|array $definition): self
    {
        if (is_callable($definition)) {
            $builder = new self($relation);
            $definition($builder);
            $this->relations[$relation] = $builder;
        } elseif ($definition instanceof FragmentInterface) {
            $this->relations[$relation] = $definition;
        } elseif (is_array($definition)) {
            $builder = new self($relation);
            $builder->select($definition);
            $this->relations[$relation] = $builder;
        }

        return $this;
    }

    public function withArguments(string $relation, array $arguments, callable|FragmentInterface|array $definition): self
    {
        if (is_callable($definition)) {
            $builder = new self($relation);
            $definition($builder);
            $this->relations[$this->buildFieldWithArgs($relation, $arguments)] = $builder;
        } elseif ($definition instanceof FragmentInterface) {
            $this->relations[$this->buildFieldWithArgs($relation, $arguments)] = $definition;
        } elseif (is_array($definition)) {
            $builder = new self($relation);
            $builder->select($definition);
            $this->relations[$this->buildFieldWithArgs($relation, $arguments)] = $builder;
        }

        return $this;
    }

    private function buildFieldWithArgs(string $field, array $arguments): string
    {
        if (empty($arguments)) {
            return $field;
        }

        $args = [];
        foreach ($arguments as $key => $value) {
            if (is_bool($value)) {
                $args[] = "$key: ".($value ? 'true' : 'false');
            } elseif (is_int($value) || is_float($value)) {
                $args[] = "$key: $value";
            } elseif (is_null($value)) {
                $args[] = "$key: null";
            } else {
                $args[] = "$key: \"$value\"";
            }
        }

        return $field.'('.implode(', ', $args).')';
    }

    public function build(): self
    {
        return $this;
    }

    public function getName(): string
    {
        return $this->name ?? $this->typeName.'Fragment';
    }

    public function getTypeName(): string
    {
        return $this->typeName;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function toGraphQL(): string
    {
        return $this->buildSelection();
    }

    public function toFragmentDefinition(): string
    {
        $selection = $this->buildSelection();

        return "fragment {$this->getName()} on {$this->typeName} {$selection}";
    }

    private function buildSelection(): string
    {
        $lines = [];

        foreach ($this->fields as $field) {
            $lines[] = $field;
        }

        foreach ($this->relations as $relation => $builder) {
            if ($builder instanceof FragmentInterface) {
                $lines[] = $relation.' '.$builder->toGraphQL();
            }
        }

        if (empty($lines)) {
            return '{ id }';
        }

        return "{\n  ".implode("\n  ", $lines)."\n}";
    }

    public function toInlineFragment(): string
    {
        return $this->buildSelection();
    }

    public function __toString(): string
    {
        return $this->toGraphQL();
    }
}
