<?php

/*
 * Copyright (C) 2019 Michael Muenz <m.muenz@gmail.com>
 * Copyright (C) 2020 Reticen8 Technologies
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

namespace Reticen8\Unbound\Api;

use Reticen8\Base\ApiMutableServiceControllerBase;
use Reticen8\Core\Backend;

class ServiceController extends ApiMutableServiceControllerBase
{
    protected static $internalServiceClass = '\Reticen8\Unbound\Unbound';
    protected static $internalServiceTemplate = 'Reticen8/Unbound/*';
    protected static $internalServiceEnabled = 'general.enabled';
    protected static $internalServiceName = 'unbound';

    public function dnsblAction()
    {
        $this->sessionClose();
        $backend = new Backend();
        $backend->configdRun('template reload ' . escapeshellarg(static::$internalServiceTemplate));
        $response = $backend->configdRun(static::$internalServiceName . ' dnsbl');
        return array('status' => $response);
    }

    /**
     * Only used on the general page to account for resolver_configure and dhcp hooks
     * since these check if unbound is enabled.
     */
    public function reconfigureGeneralAction()
    {
        $this->sessionClose();
        $backend = new Backend();
        $backend->configdRun('dns reload');
        $result = $this->reconfigureAction();
        $backend->configdRun('dhcpd restart');
        return $result;
    }
}
