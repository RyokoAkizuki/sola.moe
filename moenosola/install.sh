#!/bin/sh

make clean
make -j8
make install
service php5-fpm restart
