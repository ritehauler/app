import base64 from "base-64";
import { create } from "apisauce";
import { eventChannel, END } from "redux-saga";
import Utils from "../util";

import {
  API_LOG,
  BASE_URL,
  API_TIMEOUT,
  API_PASSWORD,
  API_USER_NAME,
  ERROR_SOMETHING_WENT_WRONG,
  ERROR_NETWORK_NOT_AVAILABLE,
  ERROR_KICK_USER
} from "../config/WebService";

const api = create({
  baseURL: BASE_URL,
  headers: {
    Authorization: `Basic ${base64.encode(`${API_USER_NAME}:${API_PASSWORD}`)}`
  },
  timeout: API_TIMEOUT
});

api.addResponseTransform(response => {
  if (typeof response.data === "string") {
    try {
      response.data = JSON.parse(response.data.trim());
    } catch (error) {}
  }
});

class ApiSauce {
  async post(url, data, headers) {
    const response = await api.post(
      url,
      { mobile_json: 1, device_type: Utils.getDeviceType(), ...data },
      { headers }
    );
    return this.handleResponse(response);
  }

  async get(url, data, headers) {
    const response = await api.get(
      url,
      { mobile_json: 1, device_type: Utils.getDeviceType(), ...data },
      { headers }
    );
    return this.handleResponse(response);
  }

  handleResponse(response) {
    if (__DEV__ && API_LOG) {
      console.log(response);
    }
    return new Promise((resolve, reject) => {
      if (
        response.ok &&
        response.data &&
        !response.data.error &&
        Object.prototype.hasOwnProperty.call(response.data, "error")
      ) {
        resolve(response.data);
      } else {
        if (response.status === 500) {
          reject(ERROR_SOMETHING_WENT_WRONG);
        } else if (response.problem === "NETWORK_ERROR") {
          reject(ERROR_NETWORK_NOT_AVAILABLE);
        } else if (
          response.data &&
          Object.prototype.hasOwnProperty.call(response.data, "error")
        ) {
          reject(response.data);
        }
        reject(ERROR_SOMETHING_WENT_WRONG);
      }
    });
  }

  postWithProgress(url, data, headers) {
    return eventChannel(emitter => {
      api
        .post(url, data, {
          headers,
          onUploadProgress: e => {
            if (__DEV__ && API_LOG) {
              console.log("onUploadProgress");
            }
            if (e.lengthComputable) {
              const progress = Math.round(e.loaded / e.total * 100);
              emitter({ progress });
            }
          }
        })
        .then(
          response => {
            if (response.ok && response.data && !response.data.error) {
              emitter({ success: response.data.data });
              emitter(END);
            } else if (response.problem === "NETWORK_ERROR") {
              emitter({ err: ERROR_NETWORK_NOT_AVAILABLE });
              emitter(END);
            } else {
              emitter({ err: ERROR_SOMETHING_WENT_WRONG });
              emitter(END);
            }
          },
          err => {
            if (err.problem === "NETWORK_ERROR") {
              emitter({ err: ERROR_NETWORK_NOT_AVAILABLE });
              emitter(END);
            } else {
              emitter({ err: ERROR_SOMETHING_WENT_WRONG });
              emitter(END);
            }
          }
        );

      return () => {};
    });
  }
}

export default new ApiSauce();
