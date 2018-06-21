<?php

namespace Tg\Decimal\Calculator;

use Tg\Decimal\Calculator\LazyCalc\CalculationOperation;
use Tg\Decimal\Rational;
use Tg\Decimal\ToRationalInterface;

class LazyCalc implements ToRationalInterface
{
    /** @var CalculationOperation */
    private $value;

    public function __construct(ToRationalInterface $value)
    {
        if ($value instanceof CalculationOperation) {
            $this->value = $value;
            return;
        }

        $this->value = new CalculationOperation($value, null, CalculationOperation::OPERATION_NO_OP);
    }

    public function add(ToRationalInterface $value): LazyCalc
    {
        return new self(new CalculationOperation($this->value, $value, CalculationOperation::OPERATION_ADD));
    }

    public function sub(ToRationalInterface $value): LazyCalc
    {
        return new self(new CalculationOperation($this->value, $value, CalculationOperation::OPERATION_SUB));
    }

    public function mul(ToRationalInterface $value): LazyCalc
    {
        return new self(new CalculationOperation($this->value, $value, CalculationOperation::OPERATION_MUL));
    }

    public function div(ToRationalInterface $value): LazyCalc
    {
        return new self(new CalculationOperation($this->value, $value, CalculationOperation::OPERATION_DIV));
    }

    public function toRational(): Rational
    {
        return $this->value->toRational();
    }

    public function calculate(): Rational
    {
        return $this->value->toRational();
    }

    public function pretty(): string
    {
        return $this->value->toPretty();
    }

    public function hint(string $hint): LazyCalc
    {
        $this->value->addHint($hint);
        return $this;
    }
}