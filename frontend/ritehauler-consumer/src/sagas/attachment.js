import { takeEvery, call, take, put } from "redux-saga/effects";

import ApiSauce from "../util/ApiSauce";
import Util from "../util";
import { Strings } from "../theme";

import { updateAttachment } from "../actions/AttachmentActions";
import { ATTACHMENT_REQUEST } from "../actions/ActionTypes";
import { API_UPLOAD_IMAGE } from "../config/WebService";

function callRequest(data) {
  return ApiSauce.postWithProgress(API_UPLOAD_IMAGE, data);
}

function* watchAttachmentRequest(action) {
  const { file, itemId } = action.payload;
  try {
    const data = new FormData(); // eslint-disable-line no-undef

    const photo = {
      uri: file,
      type: "image/jpeg",
      name: "thumb.jpg"
    };

    data.append("is_image_resize", "1");
    data.append("mobile_json", "1");
    data.append("file", photo);

    const response = yield call(callRequest, data);

    while (true) {
      const { progress = 0, success = null, err = null } = yield take(response);

      if (progress > 0) {
        yield put(updateAttachment(itemId, false, progress, undefined));
      }

      if (success) {
        if (success.attachment) {
          yield put(updateAttachment(itemId, false, 100, success.attachment));
        } else {
          yield put(updateAttachment(itemId, true, 0, undefined));
          Util.alert(Strings.errorMessageSomethingWentWrong);
        }
      }

      if (err) {
        yield put(updateAttachment(itemId, true, 0, undefined));
        Util.alert(err.message);
      }
    }
  } catch (err) {
    yield put(updateAttachment(itemId, true, 0, undefined));
    Util.alert(err.message);
  }
}

export default function* root() {
  yield takeEvery(ATTACHMENT_REQUEST, watchAttachmentRequest);
}
