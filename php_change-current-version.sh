#!/bin/bash
# ----------
# @author: f-dumas
# Description: Change php alias if there are multiple versions
# Template version: 1.0
# ----------
# shellcheck disable=SC1090
source "$(cd "$(dirname "$0")" && pwd)/../.helper_functions.sh"
#########################
# Help function
displayHelp() {
  # Command usage&
  printMessage info "-------------------
   ###TEMPLATE### Usage: $0 [-v] [-V 'my message']
-------------------"
  # Options
  printMessage info "Change php alias if there are multiple versions
  -V    Version to set as the default php alias value
  -v    Verbose mode: display a log of what the command is doing
  -t    Test mode
  "
  # Examples
  printMessage info "## Examples: ##
- $0 -V 7.2
- $0 -v
"
  exit 2
}

#########################
# Helper functions

changeCurrentVersionAlias() {
  local versionToSet=$1
  [[ -z $VERBOSE_MODE ]] || printMessage log "Set the php alias to this value $versionToSet."
  if [ ! -e "/usr/bin/php$versionToSet" ]; then
    printMessage error "Your php version is not found: '$versionToSet'"
    exit 2
  fi
  # Update the alias
  [[ -z $VERBOSE_MODE ]] || printMessage log "Update the php alias with update-alternative"
  sudo update-alternatives --set php "/usr/bin/php$versionToSet"
  addResult "$(printMessage success "Alias updated for '$versionToSet'")"
}

#########################
# Main script
#########################

# Handle command arguments
while getopts 'V:v?ht' option; do
  case $option in
  V)
    PHP_VERSION=$OPTARG
    ;;
  v)
    VERBOSE_MODE=1 && printMessage info "OPTION: Verbose mode enabled"
    ;;
  t) debugTest ;;
  h | ?) displayHelp ;; esac
done

# Start
if [[ -n $PHP_VERSION ]]; then
  changeCurrentVersionAlias "$PHP_VERSION"
fi

# End
[[ -z $VERBOSE_MODE ]] || printMessage log "Script ended with success."
renderResult && exit
