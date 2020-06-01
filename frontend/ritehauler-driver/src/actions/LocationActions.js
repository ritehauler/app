// @flow

import {
  CURRENT_LOCATION,
  TRACKING,
  START_BACKGROUND_LOCATION,
  STOP_BACKGROUND_LOCATION
} from "./ActionTypes";

export function currentLocation(location: Object) {
  return {
    location,
    type: CURRENT_LOCATION
  };
}

export function setTrackingStatus(status: boolean) {
  return {
    status,
    type: TRACKING
  };
}

export function startBackgroundLocationService() {
  return {
    type: START_BACKGROUND_LOCATION
  };
}

export function stopBackgroundLocationService() {
  return {
    type: STOP_BACKGROUND_LOCATION
  };
}
