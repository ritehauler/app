import Utils from "../util";
import { PLATFORM_TYPE_CUSTOM } from "../config/WebService";

export default {
  getUserName(user) {
    const { full_name } = user;
    return full_name || "";
    //const { first_name, last_name, full_name } = user;
    //return `${first_name}  ${last_name}`;
  },
  getUserImage(user) {
    const { gallery, auth } = user;
    if (auth.platform_type === PLATFORM_TYPE_CUSTOM) {
      return Utils.getImageFromGallery(gallery);
    }
    return `http://graph.facebook.com/${auth.platform_id}/picture?type=large`;
  },
  isCustomUser(user) {
    const { auth } = user;
    return auth.platform_type === PLATFORM_TYPE_CUSTOM;
  },
  isOrderNotificationEnable(user) {
    const { is_notify } = user;
    return is_notify && is_notify.value && is_notify.value === "1";
  },
  isAppNotificationEnable(user) {
    const { system_notify } = user;
    return system_notify && system_notify.value && system_notify.value === "1";
  },
  checkHasNotificationToken(user) {
    const { auth } = user;
    return auth.device_token && auth.device_token !== "";
  }
};
