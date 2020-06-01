import { create } from "apisauce";
import {
  API_LOG,
  API_TIMEOUT,
  ERROR_SOMETHING_WENT_WRONG,
  ERROR_NETWORK_NOT_AVAILABLE
} from "../config/WebService";

const googleApi = create({
  baseURL: "https://maps.googleapis.com",
  timeout: API_TIMEOUT
});

class GoogleApiSauce {
  async get(url, data) {
    const response = await googleApi.get(url, data);
    return this.handleResponse(response);
  }

  handleResponse(response) {
    if (__DEV__ && API_LOG) {
      console.log(response);
    }
    return new Promise((resolve, reject) => {
      if (response.ok && response.data && !response.data.error) {
        resolve(response.data);
      } else {
        if (response.status === 500) {
          reject(ERROR_SOMETHING_WENT_WRONG);
        } else if (response.problem === "NETWORK_ERROR") {
          reject(ERROR_NETWORK_NOT_AVAILABLE);
        }
        reject(response.data || ERROR_SOMETHING_WENT_WRONG);
      }
    });
  }
}
export default new GoogleApiSauce();
