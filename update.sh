#!/bin/sh

git fetch upstream
for BRANCH in MOODLE_{19..27}_STABLE master; do
	git push origin refs/remotes/upstream/$BRANCH:$BRANCH
done
