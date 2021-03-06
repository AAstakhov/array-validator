<?php

namespace Aa\ArrayValidator\Tests;

use Aa\ArrayValidator\ConstraintReader;
use Aa\ArrayValidator\Validator;
use Aa\ArrayValidator\YamlFixtureAwareTrait;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidatorTest extends PHPUnit_Framework_TestCase
{
    use YamlFixtureAwareTrait;

    /**
     * @dataProvider dataProvider
     *
     * @param array $validationOptions
     * @param array $array
     * @param array $constraintsData
     * @param array $violationsData
     */
    public function testValidate(array $validationOptions, array $array, array $constraintsData, array $violationsData)
    {
        $reader = new ConstraintReader();
        $constraints = $reader->read($constraintsData);

        $validator = new Validator();
        $validator->setIgnoreItemsWithoutConstraints($validationOptions['ignore_unexpected_items']);
        $violations = $validator->validate($array, $constraints);

        $this->assertEquals($this->getViolationsAsArray($violations), $violationsData);
    }

    public function dataProvider()
    {
        return $this->getDataFromFixtureFile('validator', __DIR__.'/fixtures');
    }

    private function getViolationsAsArray(ConstraintViolationListInterface $violations)
    {
        $result = [];

        /** @var ConstraintViolationInterface $violation */
        foreach ($violations as $violation) {
            $result[] = [
                'key_path' => $violation->getPropertyPath(),
                'invalid_value' => is_array($violation->getInvalidValue()) ? 'Array' : $violation->getInvalidValue(),
                'message' => $violation->getMessage(),
            ];
        }

        return $result;
    }
}
