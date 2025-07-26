#!/usr/bin/env bash

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

TAG_PATCH_NGINX="$REPO:nginx-$CURRENT_TAG"
TAG_MINOR_NGINX="$REPO:nginx-${MAJOR}.${MINOR}"
TAG_MAJOR_NGINX="$REPO:nginx-${MAJOR}"

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

docker build \
  --target nginx \
  --tag "$TAG_PATCH_NGINX" \
  --tag "$TAG_MINOR_NGINX" \
  --tag "$TAG_MAJOR_NGINX" \
  $( $IS_HIGHEST && echo "--tag $REPO:nginx-latest" ) \
  .

docker push "$TAG_PATCH_NGINX"
docker push "$TAG_MINOR_NGINX"
docker push "$TAG_MAJOR_NGINX"
$IS_HIGHEST && docker push "$REPO:nginx-latest"
