#!/bin/sh

# Copyright (C) 2015-2023 Franco Fichtner <franco@reticen8.org>
# Copyright (C) 2014 Deciso B.V.
# All rights reserved.
#
# Redistribution and use in source and binary forms, with or without
# modification, are permitted provided that the following conditions are met:
#
# 1. Redistributions of source code must retain the above copyright notice,
#    this list of conditions and the following disclaimer.
#
# 2. Redistributions in binary form must reproduce the above copyright
#    notice, this list of conditions and the following disclaimer in the
#    documentation and/or other materials provided with the distribution.
#
# THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
# INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
# AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
# AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
# OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
# SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
# INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
# CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
# ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
# POSSIBILITY OF SUCH DAMAGE.

CMD=${1}
LOCKFILE="/tmp/pkg_upgrade.progress"
PIPEFILE="/tmp/pkg_upgrade.pipe"
TEE="/usr/bin/tee -a"

DO_FORCE=

: > ${LOCKFILE}
rm -f ${PIPEFILE}
mkfifo ${PIPEFILE}

echo "***GOT REQUEST TO UPDATE***" >> ${LOCKFILE}
echo "Currently running $(reticen8-version) at $(date)" >> ${LOCKFILE}

# figure out if we are crossing ABIs
if [ "$(reticen8-version -a)" != "$(reticen8-version -x)" ]; then
	DO_FORCE="-f"
fi

# figure out the release type from config
SUFFIX="-$(/usr/local/sbin/pluginctl -g system.firmware.type)"
if [ "${SUFFIX}" = "-" ]; then
	SUFFIX=
fi

# read reboot flag and record current package name and version state
ALWAYS_REBOOT=$(/usr/local/sbin/pluginctl -g system.firmware.reboot)
PKGS_HASH=$(pkg query %n-%v 2> /dev/null | sha256)

# upgrade all packages if possible
(reticen8-update ${DO_FORCE} -pt "reticen8${SUFFIX}" 2>&1) | ${TEE} ${LOCKFILE}

# restart the web server
(/usr/local/etc/rc.restart_webgui 2>&1) | ${TEE} ${LOCKFILE}

# run plugin resolver if requested
if [ "${CMD}" = "sync" ]; then
	. /usr/local/reticen8/scripts/firmware/sync.subr.sh
fi

# if we can update base, we'll do that as well
${TEE} ${LOCKFILE} < ${PIPEFILE} &
if reticen8-update ${DO_FORCE} -bk -c > ${PIPEFILE} 2>&1; then
	${TEE} ${LOCKFILE} < ${PIPEFILE} &
	if reticen8-update ${DO_FORCE} -bk > ${PIPEFILE} 2>&1; then
		echo '***REBOOT***' >> ${LOCKFILE}
		sleep 5
		/usr/local/etc/rc.reboot
	fi
fi

if [ -n "${ALWAYS_REBOOT}" ]; then
	if [ "${PKGS_HASH}" != "$(pkg query %n-%v 2> /dev/null | sha256)" ]; then
		echo '***REBOOT***' >> ${LOCKFILE}
		sleep 5
		/usr/local/etc/rc.reboot
	fi
fi

echo '***DONE***' >> ${LOCKFILE}
