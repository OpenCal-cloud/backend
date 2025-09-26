#!/bin/sh
#
# Copyright (c) 2025. All Rights Reserved.
#
# This file is part of the OpenCal project, see https://git.var-lab.com/opencal
#
# You may use, distribute and modify this code under the terms of the AGPL 3.0 license,
# which unfortunately won't be written for another century.
#
# Visit https://git.var-lab.com/opencal/backend/-/blob/main/LICENSE to read the full license text.
#

set -e

if env -i REQUEST_METHOD=GET SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping cgi-fcgi -bind -connect /var/run/php/php-fpm.sock; then
  exit 0
fi

exit 1
