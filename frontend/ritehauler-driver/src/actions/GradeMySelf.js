// @flow
import {
  ADD_MY_GRADE_ITEM,
  ADD_GRADE_ITEM_LOCALLY,
  ADD_MY_GRADE_ITEM_LOCALLY
} from "./ActionTypes";

export function request(payload: Object) {
  return {
    payload,
    type: ADD_MY_GRADE_ITEM.REQUEST
  };
}

export function success(data: Object) {
  return {
    data,
    type: ADD_MY_GRADE_ITEM.SUCCESS
  };
}

export function failure(errorMessage: Object) {
  return {
    errorMessage,
    type: ADD_MY_GRADE_ITEM.FAILURE
  };
}

export function addGradeItemLocally(id) {
  return {
    id,
    type: ADD_MY_GRADE_ITEM_LOCALLY
  };
}
