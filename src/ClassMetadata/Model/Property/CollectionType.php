<?php

namespace Big\Hydrator\ClassMetadata\Model\Property;

use Big\Hydrator\{ExpressionEvaluator, MethodInvoker};
use Big\Hydrator\ClassMetadata\Model\ItemClassCandidate;

/**
 * @author kko
 */
final class CollectionType extends Leaf
{
    private ?string $adder = null;
    private ?string $itemsClass = null;
    private array $itemClassCandidates = [];


    public function isCollection() : bool
    {
        return true;
    }

    /*public function isObject() : bool
    {
        return null !== $this->itemsClass || count($this->itemClassCandidates);
    }*/

    public function hasAdder() : bool
    {
        return null !== $this->adder;
    }

    public function getAdder() : ?string
    {
        return $this->adder;
    }

    public function setAdder(string $adder) : self
    {
        $this->adder = $adder;

        return $this;
    }

    public function getItemsClass() : string
    {
        return $this->itemsClass;
    }

    public function setItemsClass(string $itemsClass) : self
    {
        $this->itemsClass = $itemsClass;

        return $this;
    }

    public function hasItemClassCandidates() : bool
    {
        return count($this->itemClassCandidates);
    }

    public function getItemClassCandidates() : array
    {
        return $this->itemClassCandidates;
    }

    public function addItemClassCandidate(ItemClassCandidate $itemClassCandidate) : self
    {
        $this->itemClassCandidates[] = $itemClassCandidate;

        return $this;
    }

    public function getCurrentItemCollectionClass(ExpressionEvaluator $expressionEvaluator, MethodInvoker $methodInvoker) : ?string
    {
        foreach ($this->itemClassCandidates as $itemClassCandidate) {
            $value = $itemClassCandidate->getDiscriminator();

            if ($value->isExpression() && true === $expressionEvaluator->resolveAdvancedExpression($value)) {
                return $itemClassCandidate->getClass();
            }

            if ($value->isMethod() && true === $methodInvoker->invokeMethod($value)) {
                return $itemClassCandidate->getClass();
            }
        }

        return $this->itemsClass;
    }
}
