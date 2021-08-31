<?php

namespace Litegram\Support;

use Respect\Validation\Factory;
use Respect\Validation\Exceptions\ComponentException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Rules\AllOf;

use function count;

/**
 * Documentation see bellow.
 *
 * @see https://respect-validation.readthedocs.io/en/latest/
 */
final class Validate extends AllOf
{
    /**
     * Create instance validator.
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * {@inheritDoc}
     */
    public function check($input): void
    {
        try {
            parent::check($input);
        } catch (ValidationException $exception) {
            if (count($this->getRules()) == 1 && $this->template) {
                $exception->updateTemplate($this->template);
            }

            throw $exception;
        }
    }

    /**
     * Creates a new Validator instance with a rule that was called on the static method.
     *
     * @param mixed[] $arguments
     *
     * @throws ComponentException
     */
    public static function __callStatic(string $ruleName, array $arguments): self
    {
        return self::create()->__call($ruleName, $arguments);
    }

    /**
     * Create a new rule by the name of the method and adds the rule to the chain.
     *
     * @param mixed[] $arguments
     *
     * @throws ComponentException
     */
    public function __call(string $ruleName, array $arguments): self
    {
        $this->addRule(Factory::getDefaultInstance()->rule($ruleName, $arguments));

        return $this;
    }
}