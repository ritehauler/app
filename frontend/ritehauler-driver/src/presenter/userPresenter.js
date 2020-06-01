// @flow
import {
  API_WORKER_PROFILE,
  API_WORKER_LOCATION_UPDATE,
  API_WORKER_STATUS_UPDATE,
  API_AUTH_LOGIN,
  API_AUTH_SEND_CODE,
  API_AUTH_VERIFY_CODE,
  API_LOGOUT
} from "../config/WebService";
import { MENU_TYPE, WORKER_TYPE } from "../constant";
import Util from "../util";

class UserPresenter {
  userRequest;
  user;

  init(userRequest, user) {
    this.userRequest = userRequest;
    this.user = user;
  }

  getUserProfilePayload() {
    return {
      device_type: Util.getPlatform()
    };
  }

  sendProfileRequest() {
    const payload = this.getUserProfilePayload();
    this.userRequest(API_WORKER_PROFILE, payload);
  }

  sendLocationRequest(locationRequest, location) {
    const payload = {
      latitude: location.latitude,
      longitude: location.longitude,
      device_type: Util.getPlatform()
    };
    locationRequest(API_WORKER_LOCATION_UPDATE, payload);
  }

  sendLoginRequest(userEmail, userPassword, deviceToken) {
    const payload = {
      email: userEmail,
      password: userPassword,
      remember_me: 1,
      device_type: Util.getPlatform(),
      device_token: deviceToken
    };
    this.userRequest(API_AUTH_LOGIN, payload);
  }

  sendVerificationCodeRequest() {
    const payload = {
      device_type: Util.getPlatform()
    };
    this.userRequest(API_AUTH_SEND_CODE, payload);
  }

  sendVerifiedCodeRequest(verificationCode) {
    const payload = {
      verification_code: verificationCode,
      device_type: Util.getPlatform()
    };
    this.userRequest(API_AUTH_VERIFY_CODE, payload);
  }

  sendDutyStatusRequest(shiftStatus) {
    const payload = {
      shift_status: shiftStatus,
      device_type: Util.getPlatform()
    };
    this.userRequest(API_WORKER_STATUS_UPDATE, payload);
  }

  sendLogoutRequest() {
    const payload = {
      device_type: Util.getPlatform()
    };
    this.userRequest(API_LOGOUT, payload);
  }

  setToken(data: Object) {
    const token = Util.getToken();
    if (!token && Util.isEmpty(token)) {
      Util.setToken(data.token);
    }
  }

  getUserProfile(data: Object) {
    return data.profile && !Util.isEmpty(data.profile)
      ? data.profile.workers
      : data.workers;
  }

  getUserJoinDate(data: Object) {
    const worker = this.getUserProfile(data);
    return !Util.isEmpty(worker) ? worker.join_date : Util.getCurrentDate();
  }

  getWorkerType(data: Object) {
    const worker = this.getUserProfile(data);
    return !Util.isEmpty(worker) ? worker.worker_type : "";
  }

  isWorkerAgent(data: Object) {
    const workerType = this.getWorkerType(data);
    return workerType === WORKER_TYPE.AGENT;
  }

  getUserProfileHashId(data: Object) {
    let hashId = "";

    if (
      data &&
      data.profile &&
      !Util.isEmpty(data.profile) &&
      data.profile.documents &&
      data.profile.documents.length > 0
    ) {
      hashId = data.profile.documents[0].hash_id;
    } else if (data && data.documents) {
      hashId = data.documents;
    }

    return hashId;
  }

  getToken(data: Object) {
    let token = "";
    if (data && data.token && !Util.isEmpty(data.token)) {
      token = data.token;
    }

    return token;
  }

  getUserImageSource(data: Object, defaultImage: Object) {
    const userHashId = this.getUserProfileHashId(data);
    // const token = this.getToken(data);
    return Util.getImageSource(userHashId, defaultImage);
  }

  isDutyToggleValue(data: Object) {
    const workers = this.getUserProfile(data);

    return workers && !Util.isEmpty(workers)
      ? workers.shift_status === "online"
      : false;
  }

  isAtWarehouse(currentLocation: Object, warehouseLocation: Object) {
    return true;
  }

  getUserName(data: Object) {
    const workers = this.getUserProfile(data);
    return this._getUserName(workers);
  }

  _getUserName(workers: Object) {
    let name = "";
    if (workers && !Util.isEmpty(workers)) {
      const { first_name, last_name } = workers;
      name = first_name + " " + last_name;
    }

    return name;
  }

  updateSecurityDeposit(menuList: any, amount: string) {
    if (menuList && !Util.isEmpty(menuList)) {
      for (const menu of menuList) {
        if (menu.type === MENU_TYPE.WALLET) {
          menu.rightText = amount;
        }
      }
    }
  }

  updateWorkerDuty(menuList: any, isOnline: boolean) {
    if (menuList && !Util.isEmpty(menuList)) {
      for (const menu of menuList) {
        if (menu.type === MENU_TYPE.DUTY) {
          menu.toggle.value = isOnline;
        }
      }
    }
  }
}

export default new UserPresenter();
