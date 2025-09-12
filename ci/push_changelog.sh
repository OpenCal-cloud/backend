#!/usr/bin/env bash

set -xe

git config --global user.email "gitlab-ci@var-lab.com"
git config --global user.name "GitLab CI"

curl -H "PRIVATE-TOKEN: $CI_API_TOKEN" "$CI_API_V4_URL/projects/$CI_PROJECT_ID/repository/changelog?version=$CI_COMMIT_TAG" | jq -r .notes > release_notes.md
git clone https://oauth2:$OPENCAL_PUSH_RELEASE_NOTES_ACCESS_TOKEN@git.var-lab.com/opencal/documentation.git documentation
cd documentation
git checkout -b "add_changelog_$CI_COMMIT_TAG"
mkdir -p docs/changelog/backend/
mv ../release_notes.md "docs/changelog/backend/$CI_COMMIT_TAG.md"
git add "docs/changelog/backend/$CI_COMMIT_TAG.md"
git commit -m "add changelog file docs/changelog/backend/$CI_COMMIT_TAG.md"
git push -u origin "add_changelog_$CI_COMMIT_TAG"
