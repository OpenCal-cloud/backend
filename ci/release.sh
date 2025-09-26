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

CURRENT_TAG=$(echo "$CI_COMMIT_TAG" | sed 's/^v//')

HIGHEST_TAG=$(git tag | grep -E '^v?[0-9]+\.[0-9]+\.[0-9]+$' | sed 's/^v//' | sort -V | tail -n1)

echo "Current tag: $CURRENT_TAG"
echo "Highest tag: $HIGHEST_TAG"

MAJOR=$(echo "$CURRENT_TAG" | cut -d. -f1)
MINOR=$(echo "$CURRENT_TAG" | cut -d. -f2)
PATCH=$(echo "$CURRENT_TAG" | cut -d. -f3)

REPO="git.var-lab.com:5050/opencal/backend"

TAG_PATCH="$REPO:$CURRENT_TAG"
TAG_MINOR="$REPO:${MAJOR}.${MINOR}"
TAG_MAJOR="$REPO:${MAJOR}"

IS_HIGHEST=false
if [ "$CURRENT_TAG" = "$HIGHEST_TAG" ]; then
  IS_HIGHEST=true
  echo "→ Current tag is the highest → will also tag as 'latest'"
fi

docker build \
  --target php \
  --tag "$TAG_PATCH" \
  --tag "$TAG_MINOR" \
  --tag "$TAG_MAJOR" \
  $( $IS_HIGHEST && echo "--tag $REPO:latest" ) \
  .

docker push "$TAG_PATCH"
docker push "$TAG_MINOR"
docker push "$TAG_MAJOR"
$IS_HIGHEST && docker push "$REPO:latest"
