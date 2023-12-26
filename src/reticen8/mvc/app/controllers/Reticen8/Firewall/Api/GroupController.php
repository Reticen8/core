<?php

/**
 *    Copyright (C) 2023 Reticen8 Technologies
 *
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
 *
 */

namespace Reticen8\Firewall\Api;

use Reticen8\Base\ApiMutableModelControllerBase;
use Reticen8\Base\UserException;
use Reticen8\Core\Backend;
use Reticen8\Core\Config;

/**
 * @package Reticen8\Firewall
 */
class GroupController extends ApiMutableModelControllerBase
{
    protected static $internalModelName = 'group';
    protected static $internalModelClass = 'Reticen8\Firewall\Group';

    /**
     * search groups
     * @return array search results
     * @throws \ReflectionException
     */
    public function searchItemAction()
    {
        return $this->searchBase("ifgroupentry", ['ifname', 'descr', 'members', 'sequence'], "ifname");
    }

    /**
     * Update group with given properties
     * @param string $uuid internal id
     * @return array save result + validation output
     * @throws \Phalcon\Filter\Validation\Exception when field validations fail
     * @throws \ReflectionException when not bound to model
     */
    public function setItemAction($uuid)
    {
        $refactored = false;
        $node = $this->getModel()->getNodeByReference('ifgroupentry.' . $uuid);
        $old_name = $node != null ? (string)$node->ifname : null;
        if ($old_name !== null && $this->request->isPost() && $this->request->hasPost("group")) {
            $new_name = $this->request->getPost("group")['ifname'];
            if ($new_name != $old_name) {
                // replace groups, setBase() will synchronise the changes to disk
                $refactored = $this->getModel()->refactor($old_name, $new_name);
            }
        }
        $result = $this->setBase("group", "ifgroupentry", $uuid);
        if ($refactored) {
            // interface renamed
            (new Backend())->configdRun('interface invoke registration');
        }
        return $result;
    }

    /**
     * Add new group and set with attributes from post
     * @return array save result + validation output
     * @throws \Reticen8\Base\ModelException when not bound to model
     * @throws \Phalcon\Filter\Validation\Exception when field validations fail
     * @throws \ReflectionException when not bound to model
     */
    public function addItemAction()
    {
        return $this->addBase("group", "ifgroupentry");
    }

    /**
     * Retrieve group settings or return defaults for new one
     * @param $uuid item unique id
     * @return array group content
     * @throws \ReflectionException when not bound to model
     */
    public function getItemAction($uuid = null)
    {
        return $this->getBase("group", "ifgroupentry", $uuid);
    }

    /**
     * Delete alias by uuid, save contents to tmp for removal on apply
     * @param string $uuid internal id
     * @return array save status
     * @throws \Phalcon\Filter\Validation\Exception when field validations fail
     * @throws \ReflectionException when not bound to model
     * @throws \Reticen8\Base\UserException when unable to delete
     */
    public function delItemAction($uuid)
    {
        Config::getInstance()->lock();
        $node = $this->getModel()->getNodeByReference('ifgroupentry.' . $uuid);
        $node_name = $node != null ? (string)$node->ifname : null;
        $uses = $node_name != null ? $this->getModel()->whereUsed($node_name) : [];
        if (!empty($uses)) {
            $message = "";
            foreach ($uses as $key => $value) {
                $message .= htmlspecialchars(sprintf("\n[%s] %s", $key, $value), ENT_NOQUOTES | ENT_HTML401);
            }
            $message = sprintf(gettext("Cannot delete group. Currently in use by %s"), $message);
            throw new \Reticen8\Base\UserException($message, gettext("Group in use"));
        }
        return $this->delBase("ifgroupentry", $uuid);
    }

    /**
     * reconfigure groups
     */
    public function reconfigureAction()
    {
        if ($this->request->isPost()) {
            (new Backend())->configdRun("filter reload skip_alias");
            (new Backend())->configdRun('interface invoke registration');
            return array("status" => "ok");
        } else {
            return array("status" => "failed");
        }
    }
}