import { put, call, take, fork } from "redux-saga/effects";
import { Actions } from "react-native-router-flux";

import ApiSauce from "../util/ApiSauce";
import Util from "../util";

import { success, failure } from "../actions/UserActions";
import { USER, USER_EDIT, CHANGE_PASSWORD } from "../actions/ActionTypes";
import { request as stateCityRequest } from "../actions/StateCity";
import {
  API_UPLOAD_IMAGE,
  API_USER_SIGN_IN,
  API_SOCIAL_LOGIN,
  API_USER_SIGN_UP
} from "../config/WebService";
import { getToken } from "../util/NotificationListener";

function callRequest(url, data) {
  return ApiSauce.post(url, data);
}

function* watchUserRequest() {
  while (true) {
    const { url, payload, callback } = yield take(USER.REQUEST);
    try {
      // is login
      const isLogin =
        url === API_USER_SIGN_IN ||
        url === API_SOCIAL_LOGIN ||
        url === API_USER_SIGN_UP;

      if (isLogin) {
        // get token and set payload for device token
        const token = yield getToken();
        payload.device_token = token;
      }

      // send request and get response
      const response = yield call(callRequest, url, payload);
      if (isLogin) {
        // update city and state list
        yield put(stateCityRequest({ mobile_json: 1 }));
      }
      // call callback
      if (callback) {
        callback(response.message, response.data.customer);
      }
      // send success action
      yield put(success(response.data.customer, response.message, url));
    } catch (err) {
      // send failure action
      yield put(failure(err.message, url));
      Util.alert(err.message);
    }
  }
}

function* watchChangePasswordRequest() {
  while (true) {
    const { url, payload } = yield take(CHANGE_PASSWORD.REQUEST);
    try {
      const response = yield call(callRequest, url, payload);
      Actions.pop();
      Util.alert(response.message, "success");
    } catch (err) {
      yield put(failure(err.message, url));
      Util.alert(err.message);
    }
  }
}

function* watchUserEditRequest() {
  while (true) {
    const { url, payload } = yield take(USER_EDIT.REQUEST);
    try {
      let responseAttachment;
      if (payload.image) {
        const data = new FormData(); // eslint-disable-line no-undef
        const photo = {
          uri: payload.image.uri,
          type: "image/jpeg",
          name: "thumb.jpg"
        };

        data.append("mobile_json", "1");
        data.append("file", photo);
        responseAttachment = yield call(callRequest, API_UPLOAD_IMAGE, data);
      }
      const { image, ...rest } = payload;
      const editProfilePayload =
        responseAttachment && responseAttachment.data
          ? {
              ...rest,
              gallery_items: responseAttachment.data.attachment.attachment_id
            }
          : { ...rest };

      const response = yield call(callRequest, url, editProfilePayload);
      yield put(success(response.data.customer, response.message, url));
      Util.alert(response.message, "success");
    } catch (err) {
      yield put(failure(err.message, url));
      Util.alert(err.message);
    }
  }
}

export default function* root() {
  yield fork(watchUserRequest);
  yield fork(watchUserEditRequest);
  yield fork(watchChangePasswordRequest);
}
