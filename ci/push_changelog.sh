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

git config --global user.email "gitlab-ci@var-lab.com"
git config --global user.name "GitLab CI"

curl -H "PRIVATE-TOKEN: $CI_API_TOKEN" "$CI_API_V4_URL/projects/$CI_PROJECT_ID/repository/changelog?version=$CI_COMMIT_TAG" | jq -r .notes > release_notes.md

git clone https://oauth2:$OPENCAL_PUSH_RELEASE_NOTES_ACCESS_TOKEN@git.var-lab.com/opencal/documentation.git documentation
cd documentation
git fetch origin

if git show-ref --verify --quiet refs/heads/add_changelog_$CI_COMMIT_TAG; then
  git branch -D add_changelog_backend_$CI_COMMIT_TAG
fi
git checkout -b "add_changelog_backend_$CI_COMMIT_TAG"

mkdir -p docs/changelog/backend/
cp ../release_notes.md "docs/changelog/backend/$CI_COMMIT_TAG.md"
git add "docs/changelog/backend/$CI_COMMIT_TAG.md"
git commit -m "add changelog file docs/changelog/backend/$CI_COMMIT_TAG.md"
git push -u origin "add_changelog_backend_$CI_COMMIT_TAG"

curl -sS -v \
  -X POST \
  -H "PRIVATE-TOKEN: $OPENCAL_PUSH_RELEASE_NOTES_ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d "{
    \"source_branch\": \"add_changelog_backend_$CI_COMMIT_TAG\",
    \"target_branch\": \"main\",
    \"title\": \"Changelog backend $CI_COMMIT_TAG\"
  }" \
  "$CI_API_V4_URL/projects/96/merge_requests" | jq
