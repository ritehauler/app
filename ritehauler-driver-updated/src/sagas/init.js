import { LOAD } from "redux-storage";
import { take, fork, select } from "redux-saga/effects";
import { Actions } from "react-native-router-flux";
import { getUser } from "../reducers/selectors";

function* watchReduxLoadFromDisk() {
  while (true) {
    yield take(LOAD);
    try {
      const { data } = yield select(getUser);
      if (
        data.auth &&
        data.auth.entity_auth_id &&
        data.entity_id &&
        data.auth.status !== 0
      ) {
        Actions.home();
      }
    } catch (err) {
      console.warn("saga watchReduxLoadFromDisk error: ", err);
    }
  }
}

export default function* root() {
  yield fork(watchReduxLoadFromDisk);
}
