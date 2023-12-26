<?php

/*
 * Copyright (C) 2022 Reticen8 Technologies
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
 * AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace Reticen8\IPsec\Api;

use Reticen8\Base\ApiMutableModelControllerBase;

/**
 * Class ManualSPD
 * @package Reticen8\IPsec\Api
 */
class ManualSPDController extends ApiMutableModelControllerBase
{
    protected static $internalModelName = 'swanctl';
    protected static $internalModelClass = 'Reticen8\IPsec\Swanctl';

    public function searchAction()
    {
        return $this->searchBase(
            'SPDs.SPD',
            ['enabled', 'description', 'origin', 'reqid', 'connection_child', 'source', 'destination']
        );
    }

    public function setAction($uuid = null)
    {
        return $this->setBase('spd', 'SPDs.SPD', $uuid);
    }

    public function addAction()
    {
        return $this->addBase('spd', 'SPDs.SPD');
    }

    public function getAction($uuid = null)
    {
        return $this->getBase('spd', 'SPDs.SPD', $uuid);
    }

    public function toggleAction($uuid, $enabled = null)
    {
        return $this->toggleBase('SPDs.SPD', $uuid, $enabled);
    }

    public function delAction($uuid)
    {
        return $this->delBase('SPDs.SPD', $uuid);
    }
}
