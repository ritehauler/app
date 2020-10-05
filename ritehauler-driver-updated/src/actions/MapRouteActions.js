// @flow
import { MAP_ROUTE, MAP_ROUTE_RESET } from "./ActionTypes";

export const RESET = "RESET";

export function request(url: string, payload: Object) {
  return {
    url,
    payload,
    type: MAP_ROUTE.REQUEST
  };
}

export function success(data: Object, url: string) {
  return {
    data,
    url,
    type: MAP_ROUTE.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: MAP_ROUTE.FAILURE
  };
}

export function reset(data: Object) {
  return {
    data,
    type: MAP_ROUTE_RESET
  };
}
