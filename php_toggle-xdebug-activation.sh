#!/bin/bash
# ----------
# @author: f-dumas
# Description: This script toggle the xdebug activation
# Template version: 1.0
# ----------
# shellcheck disable=SC1090
source "$(cd "$(dirname "$0")" && pwd)/../.helper_functions.sh"
#########################
# Help
displayHelp() {
  # Command usage&
  printMessage info "-------------------
   Usage: $0 [-v] -p 'path/to/xdebug.ini'
-------------------"
  # Options
  printMessage info "Toggle the xdebug activation in your php-fm configuration
  -p    Path to xdebug config file
  -v    Verbose mode: display a log of what the command is doing
  -t    Test mode
  "
  # Examples
  printMessage info "## Examples: ##
- $0 -v
- $0 -p '/etc/php-fpm/mods-available/xdebug.ini'
"
  exit 2
}

#########################
# Helper functions

disableXdebug() {
  # Comment each line of the config file
  [[ -z $VERBOSE_MODE ]] || printMessage log "Commenting all lines of the config file..."
  sudo sed -i 's/^/#/' "$XDEBUG_PATH" && export XDEBUG_CONFIG="remote_enable=0"
  # Enqueue the result message
  addResult "$(printMessage success "xDebug is successfully disabled.")"
}

enableXdebug() {
  # Comment each line of the config file
  [[ -z $VERBOSE_MODE ]] || printMessage log "Un-commenting all lines of the config file..."
  sudo sed -i 's/^#//' "$XDEBUG_PATH" && export XDEBUG_CONFIG="remote_enable=1"
  # Enqueue the result message
  addResult "$(printMessage success "xDebug is successfully enabled.")"
}

restartPhpFpm() {
  # Check current php version
  local currentPhpVersion
  currentPhpVersion=$(php -v | grep -Po '(?!PHP )(7.[0-9]+)' -m 1);
  [[ -z $VERBOSE_MODE ]] || printMessage log "Current php-fpm version: $currentPhpVersion"
  # Restart php7X-fpm
  [[ -z $VERBOSE_MODE ]] || printMessage log "Restarting php$currentPhpVersion-fpm."
  sudo service "php$currentPhpVersion-fpm" restart
  addResult "$(printMessage success "php$currentPhpVersion-fpm has been restarted successfully")"
}

#########################
# Main script
#########################

# Handle command arguments
while getopts 'p:v?ht' option; do
  case $option in
  p)
    XDEBUG_PATH=$OPTARG
    # Check if the given file exists
    if [[ ! -e $XDEBUG_PATH ]]; then
      printMessage error "The given file '$XDEBUG_PATH' does not exists."
      exit 2
    fi
    ;;
  v)
    VERBOSE_MODE=1 && printMessage info "OPTION: Verbose mode enabled"
    ;;
  t) debugTest ;;
  h | ?) displayHelp ;; esac
done

# Check current xDebug config
[[ -z $VERBOSE_MODE ]] || printMessage log "xDebug current config: $XDEBUG_PATH"
[[ -z $VERBOSE_MODE ]] || printMessage log "File content: \n$(<"$XDEBUG_PATH")"
if php -m | grep -q 'xdebug'; then
  # Xdebug is enabled
  [[ -z $VERBOSE_MODE ]] || printMessage log "xDebug is currently enabled."
  disableXdebug
else
  #xDebug is disabled
  [[ -z $VERBOSE_MODE ]] || printMessage log "xDebug is currently disabled."
  enableXdebug
fi
# Restart php-fpm
restartPhpFpm

# End
[[ -z $VERBOSE_MODE ]] || printMessage log "Script ended with success."
renderResult && exit
