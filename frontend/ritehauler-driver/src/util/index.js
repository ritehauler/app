// @flow
import { Platform, Alert } from "react-native";
import _ from "lodash";
import moment from "moment";
import { MessageBarManager } from "react-native-message-bar";
import { isIphoneX } from "react-native-iphone-x-helper";
import { PLATFORM_TYPE_CUSTOM } from "../constant";

function deg2rad(deg) {
  return deg * (Math.PI / 180);
}

var createOrderInStack = false;
var loginUserID = undefined;

class Util {
  keyExtractor = (item: Object, index: number) => index;
  getPlatform = () => Platform.OS;
  isPlatformAndroid() {
    return Platform.OS === "android";
  }

  isEmpty(data: any) {
    return _.isEmpty(data);
  }

  getDeviceType() {
    return Platform.OS === "android" ? "android" : "ios";
  }

  getDistanceInMiles(lat2, lon2, lat1, lon1) {
    if (!lat1 || !lon1 || !lat2 || !lon2) {
      return NaN;
    }

    const R = 6371;

    const dLat = deg2rad(lat2 - lat1);
    const dLon = deg2rad(lon2 - lon1);

    const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(deg2rad(lat1)) *
        Math.cos(deg2rad(lat2)) *
        Math.sin(dLon / 2) *
        Math.sin(dLon / 2);

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    // const d = Math.round(R * c);
    const d = R * c;
    const miles = d / 1.609344;

    return miles.toFixed(1);
  }

  get12Hours(givenTime) {
    return moment(givenTime, ["HH"]).format("LT");
  }

  getRandomNumber() {
    let text = "";
    const possible =
      "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (let i = 0; i < 50; i += 1)
      text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
  }

  isValidURL(url: "string") {
    const re = /^(http|https|fttp):\/\/|[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,6}(:[0-9]{1,5})?(\/.*)?$/;
    return re.test(url);
  }
  isEmailValid(email: string) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
  }
  isPasswordValid(password: string) {
    return password.length > 5;
  }
  getValidImage(image: any) {
    if (typeof image === "string" && this.isValidURL(image)) {
      return { uri: image };
    }
    // if (typeof image === "string" && !this.isValidURL(image)) {
    //   return require(image);
    // }
    return image;
  }

  getImageFromGallery = (gallery, imageType = 0) => {
    let file;
    if (imageType === 0) {
      file = (gallery && gallery.length > 0 && gallery[0].thumb) || undefined;
    } else {
      file =
        (gallery && gallery.length > 0 && gallery[0].compressed_file) ||
        undefined;
    }
    return file;
  };

  showCommonMessage(title, message) {
    Alert.alert(title, message, [{ text: "OK", onPress: () => {} }], {
      cancelable: false
    });
  }

  isPhoneX() {
    return isIphoneX();
  }

  showMessage(message, alertType = "error") {
    MessageBarManager.showAlert({
      message,
      alertType
    });
  }

  showAlertWithDelay(title, message, delay = 500) {
    setTimeout(() => {
      this.showCommonMessage(title, message);
    }, delay);
  }

  getLocationString(location: Object) {
    let latlong = "";
    if (location) {
      latlong = location.latitude + "," + location.longitude;
    }

    return latlong;
  }

  getGoogleRouteUrl(source: string, destination: string, driveMode: string) {
    const url =
      "https://maps.google.com/maps?mode=" +
      driveMode +
      "&saddr=" +
      source +
      "&daddr=" +
      destination;

    return url;
  }

  isCreateOrderInStack() {
    return createOrderInStack;
  }

  createOrderLock(value) {
    createOrderInStack = value;
  }

  setLoginUserID(ID) {
    loginUserID = ID;
  }

  getLoginUserID() {
    return loginUserID;
  }

  getGMTDate(date) {
    const momentInstance = moment(date, "YYYY-MM-DD HH:mm:ss");
    return moment.utc(momentInstance).format("YYYY-MM-DD");
  }

  formatDate(dateString, currentDateFormat, FormattedDateFormat) {
    return moment(dateString, currentDateFormat).format(FormattedDateFormat);
  }

  formatDateLocal(dateString, currentDateFormat, FormattedDateFormat) {
    return moment
      .utc(dateString, currentDateFormat)
      .local()
      .format(FormattedDateFormat);
  }

  dateFromNow(dateString, currentDateFormat) {
    return moment(dateString, currentDateFormat).fromNow();
  }

  dateFromNowLocal(dateString, currentDateFormat) {
    return moment
      .utc(dateString, currentDateFormat)
      .local()
      .fromNow();
  }

  /* Image section */

  getUserImage(user) {
    const { gallery, auth } = user;
    if (
      auth &&
      auth.platform_type &&
      auth.platform_type === PLATFORM_TYPE_CUSTOM
    ) {
      return this.getImageFromGallery(gallery);
    }
    return `https://graph.facebook.com/${
      auth && auth.platform_id ? auth.platform_id : ""
    }/picture?type=large`;
  }

  getImageFromGallery = (gallery, imageType = 0) => {
    let file;
    if (imageType === 0) {
      file = (gallery && gallery.length > 0 && gallery[0].thumb) || undefined;
    } else {
      file =
        (gallery && gallery.length > 0 && gallery[0].mobile_file) || undefined;
    }
    return file;
  };
}

export default new Util();
