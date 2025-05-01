<?php

namespace AlgoYounes\CommissionTask\Services\Commission\Rules;

use AlgoYounes\CommissionTask\Services\Commission\Contracts\CommissionRuleContract;

class CommissionRulesRegistry
{
    private const RULES_CLASSES = [
        DepositCommissionRule::class,
        WithdrawPrivateCommissionRule::class,
        WithdrawBusinessCommissionRule::class,
    ];

    /**
     * @var array<class-string<CommissionRuleContract>>
     */
    private array $rules = [];

    public function __construct()
    {
        $this->register();
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function register(): void
    {
        foreach (self::RULES_CLASSES as $rule) {
            $this->rules[] = new $rule();
        }
    }

    public function getSupportedRule($operation): CommissionRuleContract
    {
        /** @var CommissionRuleContract $rule */
        foreach ($this->rules as $rule) {
            if ($rule->isSupported($operation)) {
                return $rule;
            }
        }

        throw new \RuntimeException('No supported commission rule found.');
    }
}
