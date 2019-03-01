#!/usr/bin/env bash
pushd $(dirname $0)
cd ..

composer install

mkdir dist/
cp bin dist/ -R
cp src dist/ -R
cp vendor dist/ -R
cp LICENSE dist/
cp README.md dist/
zip dist.zip -r dist
rm -rf dist/

popd
