// @flow
import { SUGGESTED_VEHICLES } from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: SUGGESTED_VEHICLES.REQUEST
  };
}

export function success(
  data: Object,
  payLoad: Object,
  truckSuggestedId: String,
  successMessage: String
) {
  return {
    data,
    payLoad,
    truckSuggestedId,
    successMessage,
    type: SUGGESTED_VEHICLES.SUCCESS
  };
}

export function failure(errorMessage: String) {
  return {
    errorMessage,
    type: SUGGESTED_VEHICLES.FAILURE
  };
}
