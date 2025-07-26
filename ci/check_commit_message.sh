#!/usr/bin/env bash

echo "Checking commit messages..."

VALID_TYPES="feat|fix|docs|chore"
INVALID_MESSAGES=0

for COMMIT in $(git rev-list origin/main..HEAD); do
  MESSAGE=$(git log --format=%B -n 1 "$COMMIT")
  echo "Checking commit: $COMMIT"
  echo "$MESSAGE" | grep -qE "Changelog: ($VALID_TYPES)$"
  if [ $? -ne 0 ]; then
    echo "❌ Invalid commit message in $COMMIT:"
    echo "$MESSAGE"
    INVALID_MESSAGES=1
  fi
done

if [ "$INVALID_MESSAGES" -ne 0 ]; then
  echo "❌ the commit message must contain the changelog type (Changelog: <type>). Valid types are: feat, fix, docs, chore. Please edit the commit message."
  exit 1
else
  echo "✅ Commit message validated."
fi
