#!/bin/bash

# Remove printer script
rm -f "${MUNKIPATH}preflight.d/printer.py"

# Remove printers.plist file
rm -f "${MUNKIPATH}preflight.d/cache/printer.txt"
rm -f "${MUNKIPATH}preflight.d/cache/printer.plist"
