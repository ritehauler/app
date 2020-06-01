import { create } from "apisauce";
import {
  GOOGLE_BASE_URL,
  API_TIMEOUT,
  ERROR_SOMETHING_WENT_WRONG,
  API_LOG
} from "../config/WebService";
import Util from "../util";

const api = create({
  baseURL: GOOGLE_BASE_URL,
  headers: {},
  timeout: API_TIMEOUT
});

class GoogleApiSauce {
  async post(url, data, headers) {
    const response = await api.post(url, data, { headers });

    if (__DEV__ && API_LOG) {
      console.log(response);
    }
    return new Promise((resolve, reject) => {
      if (response.ok && response.data) {
        resolve(response.data);
      } else {
        if (response.status === 500) {
          reject(ERROR_SOMETHING_WENT_WRONG);
        }
        reject(response.data || ERROR_SOMETHING_WENT_WRONG);
      }
    });
  }

  async get(url, data, headers) {
    const response = await api.get(url, data, { headers });

    if (__DEV__ && API_LOG) {
      console.log(response);
    }
    return new Promise((resolve, reject) => {
      if (response.ok && response.data) {
        resolve(response.data);
      } else {
        if (response.status === 500) {
          reject(ERROR_SOMETHING_WENT_WRONG);
        }
        reject(response.data || ERROR_SOMETHING_WENT_WRONG);
      }
    });
  }
}

export default new GoogleApiSauce();
