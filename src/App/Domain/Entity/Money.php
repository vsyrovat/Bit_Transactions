<?php
namespace App\Domain\Entity;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Intl\Exception\MethodArgumentValueNotImplementedException;

class Money
{
    // Round methods from http://docs.oracle.com/javase/7/docs/api/java/math/BigDecimal.html

    /**
     * Rounding mode to round away from zero. Always increments the digit prior to a nonzero discarded fraction.
     * Note that this rounding mode never decreases the magnitude of the calculated value.
     */
    const ROUND_UP = 1; // 1.1 -> 2; -1.1 -> -2

    /**
     * Rounding mode to round towards zero. Never increments the digit prior to a discarded fraction (i.e., truncates).
     * Note that this rounding mode never increases the magnitude of the calculated value.
     */
    const ROUND_DOWN = 2; // 1.1 -> 1; 1.9 -> 1; -1.1 -> -1; -1.9 -> -1

    /**
     * Rounding mode to round towards positive infinity. If the number is positive, behaves as for ROUND_UP;
     * if negative, behaves as for ROUND_DOWN. Note that this rounding mode never decreases the calculated value.
     */
    const ROUND_CEIL = 3; // 1.1 -> 2; -1.1 -> -1

    /**
     * Rounding mode to round towards negative infinity. If the number is positive, behave as for ROUND_DOWN;
     * if negative, behave as for ROUND_UP. Note that this rounding mode never increases the calculated value.
     */
    const ROUND_FLOOR = 4; // 1.1 -> 1; -1.1 -> -2

    /**
     * Rounding mode to round towards "nearest neighbor" unless both neighbors are equidistant,
     * in which case round up. Behaves as for ROUND_UP if the discarded fraction is ≥ 0.5;
     * otherwise, behaves as for ROUND_DOWN.
     * Note that this is the rounding mode that most of us were taught in grade school.
     */
    const ROUND_HALF_UP = 5; // 1.4 -> 1; 1.5 -> 2; 1.6 -> 2; -1.4 -> -1; -1.5 -> -2; -1.6 -> -2

    /**
     * Rounding mode to round towards "nearest neighbor" unless both neighbors are equidistant,
     * in which case round down. Behaves as for ROUND_UP if the discarded fraction is > 0.5;
     * otherwise, behaves as for ROUND_DOWN.
     */
    const ROUND_HALF_DOWN = 6; // 1.4 -> 1; 1.5 -> 1; 1.6 -> 2; -1.4 -> -1; -1.5 -> -1; -1.6 -> -2

    /**
     * Rounding mode to round towards the "nearest neighbor" unless both neighbors are equidistant,
     * in which case, round towards the even neighbor. Behaves as for ROUND_HALF_UP if the digit
     * to the left of the discarded fraction is odd; behaves as for ROUND_HALF_DOWN if it's even.
     * Note that this is the rounding mode that minimizes cumulative error when applied repeatedly
     * over a sequence of calculations.
     */
    const ROUND_HALF_EVEN = 7; // 1.4 -> 1; 1.5 -> 2; 2.5 -> 2; -1.5 -> -2; -2.5 -> -2

    protected $amountCent;
    protected $amount;
    protected $currency;

    /**
     * @param int|string $amount
     * @param string $currency
     */
    public function __construct($amount, $currency = null)
    {
        switch (gettype($amount)) {
            case 'NULL':
                $this->amountCent = null;
                goto amountDetected;
                break;
            case 'integer':
                integer:
                $this->amountCent = $amount * 100;
                goto amountDetected;
                break;
            case 'double':
                double:
                $this->amountCent = intval(round($amount * 100, 0, PHP_ROUND_HALF_EVEN));
                goto amountDetected;
                break;
            case 'string':
                if (ctype_digit($amount)
                    || (strlen($amount) > 1
                        && substr($amount, 0, 1) === '-'
                        && ctype_digit(substr($amount, 1)))
                ) {
                    goto integer;
                }
                if (preg_match('#^-?(\d+)\.(\d+)$#', "$amount", $matches)) {
                    goto double;
                }
                break;
            default:
        }

        throw new \InvalidArgumentException(
            'Unexpected format of amount, expected int or string or float in "12.34" format, '
            .$amount .'(' . gettype($amount) . ') given'
        );

        amountDetected:

        $this->amount = $this->amountFromCent($this->amountCent);
        $this->currency = $currency;
    }

    /**
     * @return int
     */
    public function getAmountCent()
    {
        return $this->amountCent;
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param self $other
     * @return bool
     */
    public function isEqual(self $other)
    {
        return $this->currency === $other->currency
            && $this->amountCent === $other->amountCent;
    }

    public function isSameCurrency(self $other)
    {
        return $this->currency === $other->currency;
    }

    public function isLessThan(self $other)
    {
        if ($this->currency !== $other->currency) {
            throw new \RuntimeException('Cannot compare different currencies: '.$this->currency.' and '.$other->currency);
        }

        return $this->amountCent < $other->amountCent;
    }

    public function isLessOrEqualThan(self $other)
    {
        if ($this->currency !== $other->currency) {
            throw new \RuntimeException('Cannot compare different currencies: '.$this->currency.' and '.$other->currency);
        }

        return $this->amountCent <= $other->amountCent;
    }

    public function isGreaterThan(self $other)
    {
        if ($this->currency !== $other->currency) {
            throw new \RuntimeException('Cannot compare different currencies: '.$this->currency.' and '.$other->currency);
        }

        return $this->amountCent > $other->amountCent;
    }

    public function isGreaterOrEqualThan(self $other)
    {
        if ($this->currency !== $other->currency) {
            throw new \RuntimeException('Cannot compare different currencies: '.$this->currency.' and '.$other->currency);
        }

        return $this->amountCent >= $other->amountCent;
    }

    /**
     * @param self $other
     * @return self
     */
    public function add(self $other)
    {
        if ($this->currency !== $other->currency
            && !(($this->currency === null && $this->amountCent === null)
                || ($other->currency === null && $other->amountCent === null))
        ) {
            throw new \RuntimeException('Cannot add different currencies: '.$this->currency.' and '.$other->currency);
        }

        $currency = is_null($this->currency)
            ? $other->currency
            : $this->currency;

        return new self($this->amountFromCent($this->amountCent + $other->amountCent), $currency);
    }

    /**
     * @param self $other
     * @return self
     */
    public function sub(self $other)
    {
        if ($this->currency !== $other->currency
            && !(($this->currency === null && $this->amountCent === null)
                || ($other->currency === null && $other->amountCent === null))
        ) {
            throw new \RuntimeException('Cannot add different currencies: '.$this->currency.' and '.$other->currency);
        }

        $currency = is_null($this->currency)
            ? $other->currency
            : $this->currency;

        return new self($this->amountFromCent($this->amountCent - $other->amountCent), $currency);
    }

    /**
     * @param int|float $multiplier
     * @param $roundMode
     * @return self
     */
    public function mul($multiplier, $roundMode = self::ROUND_HALF_EVEN)
    {
        if (is_null($this->amountCent)) {
            $newAmount = null;
        } else {
            $newValue = $this->amountCent * $multiplier;
            $newAmountCent = $this->round($newValue, $roundMode);
            $newAmount = $this->amountFromCent($newAmountCent);
        }

        return new self($newAmount, $this->currency);
    }

    /**
     * @param int|float $divisor
     * @param $roundMode
     * @return self
     */
    public function div($divisor, $roundMode = self::ROUND_HALF_EVEN)
    {
        if ($divisor == 0) {
            throw new \InvalidArgumentException("Division by zero is not possible");
        }

        if (is_null($this->amountCent)) {
            $newAmount = null;
        } else {
            $newValue = $this->amountCent / $divisor;
            $newAmountCent = $this->round($newValue, $roundMode);
            $newAmount = $this->amountFromCent($newAmountCent);
        }

        return new self($newAmount, $this->currency);
    }

    private function amountFromCent($amountCent)
    {
        if (is_null($amountCent)) {
            return null;
        }
        $isNegative = $amountCent < 0;
        $amountCent = abs($amountCent);
        $cents = $amountCent % 100;
        $dollars = floor($amountCent / 100);
        $result = $cents > 0 ? sprintf('%d.%02d', $dollars, $cents) : "$dollars";
        if ($isNegative) {
            $result = "-$result";
        }
        return $result;
    }

    private function round($value, $roundMode)
    {
        switch ($roundMode) {
            case self::ROUND_UP:
                throw new MethodArgumentValueNotImplementedException(__METHOD__, 'roundMode', 'self::ROUND_UP');
                break;
            case self::ROUND_DOWN:
                return intval($value);
                break;
            case self::ROUND_CEIL:
                return ceil($value);
                break;
            case self::ROUND_FLOOR:
                return floor($value);
                break;
            case self::ROUND_HALF_UP:
                return round($value, 0, PHP_ROUND_HALF_UP);
                break;
            case self::ROUND_HALF_DOWN:
                return round($value, 0, PHP_ROUND_HALF_DOWN);
                break;
            case self::ROUND_HALF_EVEN:
                return round($value, 0, PHP_ROUND_HALF_EVEN);
                break;
            default:
                throw new InvalidArgumentException('Unknown round mode: ' . $roundMode);
        }
    }

    /**
     * @param string|int $amount
     * @param string $currency
     * @return self
     */
    public static function create($amount, $currency = null)
    {
        return new self($amount, $currency);
    }
}
