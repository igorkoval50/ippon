<?php
/**
 * Shopware Premium Plugins
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this plugin can be used under
 * a proprietary license as set forth in our Terms and Conditions,
 * section 2.1.2.2 (Conditions of Usage).
 *
 * The text of our proprietary license additionally can be found at and
 * in the LICENSE file you have received along with this plugin.
 *
 * This plugin is distributed in the hope that it will be useful,
 * with LIMITED WARRANTY AND LIABILITY as set forth in our
 * Terms and Conditions, sections 9 (Warranty) and 10 (Liability).
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the plugin does not imply a trademark license.
 * Therefore any rights, title and interest in our trademarks
 * remain entirely with us.
 */

namespace SwagPromotion\Tests\Rules;

use PHPUnit\Framework\TestCase;
use SwagPromotion\Components\Rules\Registry\Registry;
use SwagPromotion\Components\Rules\Rule\CompareRule;
use SwagPromotion\Components\Rules\Rule\Container\AndRule;
use SwagPromotion\Components\Rules\Rule\Container\NotRule;
use SwagPromotion\Components\Rules\Rule\Container\OrRule;
use SwagPromotion\Components\Rules\Rule\FalseRule;
use SwagPromotion\Components\Rules\Rule\TrueRule;
use SwagPromotion\Components\Rules\RuleBuilder;

/**
 * @small
 */
class VariousRulesTest extends TestCase
{
    /**
     * Ensure that container constructor consumes all func arg rules - and not only the first one
     */
    public function testConstructorLength()
    {
        $rule = new OrRule(
            new FalseRule(),
            new TrueRule()
        );
        static::assertTrue($rule->validate());
    }

    /**
     * Test creating a rule tree via constructor injection
     */
    public function testConstructorContainer()
    {
        $rule = new AndRule(
            new TrueRule(),
            new OrRule(
                new FalseRule(),
                new TrueRule()
            ),
            new NotRule(
                new FalseRule()
            )
        );
        static::assertTrue($rule->validate());
    }

    /**
     * Test inversion of rule results with the NOT rule
     */
    public function testNotRule()
    {
        $rule = new NotRule();
        $rule->setRules([new TrueRule()]);

        static::assertFalse($rule->validate());

        $rule = new NotRule();
        $rule->addRule(new TrueRule());

        static::assertFalse($rule->validate());

        $rule = $this->getRuleBuilder()->fromArray(
            [
                'false',
            ],
            'not'
        );
        static::assertTrue($rule->validate());

        try {
            $rule = $this->getRuleBuilder()->fromArray(
                [
                    'false',
                    'true',
                ],
                'not'
            );
        } catch (\RuntimeException $e) {
            $rule = null;
        }
        static::assertNull($rule);
    }

    /**
     * Test logical OR rule container
     */
    public function testOrRule()
    {
        $rule = $this->getRuleBuilder()->fromArray(
            [
                'false',
                'true',
            ],
            'or'
        );
        static::assertTrue($rule->validate());

        $rule = $this->getRuleBuilder()->fromArray(
            [
                'false',
                'false',
            ],
            'or'
        );
        static::assertFalse($rule->validate());
    }

    /**
     * Test logical AND rule container
     */
    public function testAndRule()
    {
        $rule = $this->getRuleBuilder()->fromArray(
            [
                'false',
                'true',
            ],
            'and'
        );
        static::assertFalse($rule->validate());

        $rule = $this->getRuleBuilder()->fromArray(
            [
                'true',
                'true',
            ],
            'and'
        );
        static::assertTrue($rule->validate());
    }

    /**
     * Test logical XOR rule container
     */
    public function testXorRule()
    {
        $rule = $this->getRuleBuilder()->fromArray(
            [
                'false',
                'true',
                'false',
            ],
            'xor'
        );
        static::assertTrue($rule->validate());

        $rule = $this->getRuleBuilder()->fromArray(
            [
                'false',
                'false',
            ],
            'xor'
        );
        static::assertFalse($rule->validate());

        $rule = $this->getRuleBuilder()->fromArray(
            [
                'false',
                'true',
                'true',
            ],
            'xor'
        );
        static::assertFalse($rule->validate());
    }

    public function testCompareRule()
    {
        $rule = new CompareRule(3, '<=', 5);
        static::assertTrue($rule->validate());

        $rule = new CompareRule(100, '<', 4);
        static::assertFalse($rule->validate());

        $rule = new CompareRule(100, '>', 100);
        static::assertFalse($rule->validate());

        $rule = new CompareRule(100, '>=', 100);
        static::assertTrue($rule->validate());

        $rule = new CompareRule(4, '=', 4);
        static::assertTrue($rule->validate());

        $rule = new CompareRule(4, '=', 5);
        static::assertFalse($rule->validate());

        $rule = new CompareRule(4, '!=', 5);
        static::assertTrue($rule->validate());

        $rule = new CompareRule(5, '<>', 5);
        static::assertFalse($rule->validate());
    }

    public function testCompareRuleLeftOperandMissingException()
    {
        $rule = new CompareRule(null, '>', 100);

        static::expectException(\RuntimeException::class);

        static::assertFalse($rule->validate());
    }

    public function testCompareRuleRightOperandMissingException()
    {
        $rule = new CompareRule(100, '>', null);

        static::expectException(\RuntimeException::class);

        static::assertFalse($rule->validate());
    }

    public function testCompareRuleInvalidOperator()
    {
        $rule = new CompareRule(100, 'foo', 100);

        static::expectException(\RuntimeException::class);

        static::assertFalse($rule->validate());
    }

    private function getRuleBuilder()
    {
        return new RuleBuilder(
            new Registry()
        );
    }
}
