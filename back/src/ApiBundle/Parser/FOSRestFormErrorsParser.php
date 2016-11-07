<?php

namespace ApiBundle\Parser;

use Nelmio\ApiDocBundle\DataTypes;
use Nelmio\ApiDocBundle\Parser\ParserInterface;
use Nelmio\ApiDocBundle\Parser\PostParserInterface;

class FOSRestFormErrorsParser implements ParserInterface, PostParserInterface
{
    public function supports(array $item = [])
    {
        return isset($item['fos_rest_form_errors']) && $item['fos_rest_form_errors'] === true;
    }

    public function parse(array $item)
    {
        return array();
    }


    public function postParse(array $item, array $parameters)
    {
        $newParameters = [];

        // Clean all other parsers parameters
        foreach ($parameters as $key => $parameter) {
            $newParameters[$key] = null;
        }

        $newParameters['code'] = [
            'dataType' => 'integer',
            'actualType' => DataTypes::INTEGER,
            'subType' => null,
            'required' => false,
            'description' => 'The status code',
            'readonly' => true
        ];

        $newParameters['message'] = [
            'dataType' => 'string',
            'actualType' => DataTypes::STRING,
            'subType' => null,
            'required' => true,
            'description' => 'The error message',
            'default' => 'Validation failed.',
        ];

        $newParameters['errors'] = [
            'dataType' => 'errors',
            'actualType' => DataTypes::MODEL,
            'subType' => sprintf('%s.FormErrors', $item['class']),
            'required' => true,
            'description' => 'List of errors',
            'readonly' => true,
            'children' => [
                'children' => [
                    'dataType' => 'List of form fields',
                    'actualType' => DataTypes::MODEL,
                    'subType' => sprintf('%s.Children', $item['class']),
                    'required' => true,
                    'description' => 'Errors',
                    'readonly' => true,
                    'children' => []
                ]
            ]
        ];

        foreach ($parameters as $name => $parameter) {
            $newParameters['errors']['children']['children']['children'][$name] = $this->doPostParse($parameter, $name, [$name], $item['class']);
        }

        return $newParameters;
    }

    protected function doPostParse($parameter, $name, array $propertyPath, $type)
    {
        $data = [
            'dataType' => 'Form field',
            'actualType' => DataTypes::MODEL,
            'subType' => sprintf('%s.FieldErrors[%s]', $type, implode('.', $propertyPath)),
            'required' => true,
            'description' => 'Field name',
            'readonly' => true,
            'children' => [
                'errors' => [
                    'dataType' => 'errors',
                    'actualType' => DataTypes::COLLECTION,
                    'subType' => 'string',
                    'required' => false,
                    'description' => 'List of field error messages',
                    'readonly' => true
                ]
            ]
        ];

        if ($parameter['actualType'] == DataTypes::COLLECTION) {
            $data['children']['children'] = [
                'dataType' => 'List of embedded forms fields',
                'actualType' => DataTypes::COLLECTION,
                'subType' => sprintf('%s.FormErrors', $parameter['subType']),
                'required' => true,
                'description' => 'Validation error messages',
                'readonly' => true,
                'children' => [
                    'children' => [
                        'dataType' => 'Embedded form field',
                        'actualType' => DataTypes::MODEL,
                        'subType' => sprintf('%s.Children', $parameter['subType']),
                        'required' => true,
                        'description' => 'List of errors',
                        'readonly' => true,
                        'children' => []
                    ]
                ]
            ];

            foreach ($parameter['children'] as $cName => $cParameter) {
                $cPropertyPath = array_merge($propertyPath, [$cName]);

                $data['children']['children']['children']['children']['children'][$cName] = $this->doPostParse($cParameter, $cName, $cPropertyPath, $parameter['subType']);
            }
        }

        return $data;
    }
}
