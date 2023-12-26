<?php

/**
 *    Copyright (C) 2016 Reticen8 Technologies
 *    All rights reserved.
 *
 *    Redistribution and use in source and binary forms, with or without
 *    modification, are permitted provided that the following conditions are met:
 *
 *    1. Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *
 *    2. Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 *    THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
 *    INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
 *    AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 *    AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 *    OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 *    SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 *    INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 *    CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 *    ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 *    POSSIBILITY OF SUCH DAMAGE.
 */

namespace tests\Reticen8\Base\FieldTypes;

/**
 * Class Field_Framework_TestCase
 * @package tests\Reticen8\Base\FieldTypes
 */
class Field_Framework_TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Validate and throw exception
     * @param string $field field type
     * @throws \Phalcon\Filter\Validation\Exception
     */
    public function validateThrow($field)
    {
        $validation = new \Reticen8\Base\Validation();
        foreach ($field->getValidators() as $validator) {
            $validation->add("testfield", $validator);
        }

        $messages = $validation->validate(array("testfield" => (string)$field));
        if (count($messages)) {
            foreach ($messages as $message) {
                throw new \Reticen8\Phalcon\Filter\Validation\Exception($message->getType());
            }
        }
        return;
    }

    /**
     * Validate and return exceptions
     * @param string $field field type
     * @return array
     */
    public function validate($field)
    {
        $result = array();
        $validation = new \Reticen8\Base\Validation();
        foreach ($field->getValidators() as $validator) {
            $validation->add("testfield", $validator);
        }

        $messages = $validation->validate(array("testfield" => (string)$field));
        if (count($messages)) {
            foreach ($messages as $message) {
                $result[] = $message->getType();
            }
        }
        return $result;
    }

    /**
     * do not test
     * @group Ignore
     */
    public function testIgnore()
    {
        return;
    }
}