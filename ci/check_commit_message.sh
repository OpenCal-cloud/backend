#!/usr/bin/env bash

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

set -xe

echo "Checking commit messages..."

VALID_TYPES="feat|fix|docs|chore"
INVALID_MESSAGES=0

MESSAGE=$(git log --format=%B -n 1 | cat)
echo "Checking commit: $COMMIT"
echo "$MESSAGE" | grep -qE "Changelog: ($VALID_TYPES)$"
if [ $? -ne 0 ]; then
  echo "❌ Invalid commit message in $COMMIT:"
  echo "$MESSAGE"
  INVALID_MESSAGES=1
fi

if [ "$INVALID_MESSAGES" -ne 0 ]; then
  echo "❌ the commit message must contain the changelog type (Changelog: <type>). Valid types are: feat, fix, docs, chore. Please edit the commit message."
  exit 1
else
  echo "✅ Commit message validated."
fi
