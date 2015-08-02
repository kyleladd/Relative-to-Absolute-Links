# Relative-to-Absolute-Links
Covert all relative links within string to absolute urls

[![Build Status](https://travis-ci.org/kyleladd/Relative-to-Absolute-Links.svg)](https://travis-ci.org/kyleladd/Relative-to-Absolute-Links)

###Installation

###Usage
convertRelativeToAbsolute($mystring,$url,$isStartingSlashRoot,$rootURL);

###Configuration
####mystring
The string that contains the relative links.

####url
The absolute url to adjust the relative links with.

####isStartingSlashRoot
Windows servers: Links that start with a slash means the current directory
Linux servers: Links that start with a slash means go to root 

####rootURL
root url will default to the top level domain
