import { Platform, findNodeHandle } from "react-native";
import { MessageBarManager } from "react-native-message-bar";
import { Actions } from "react-native-router-flux";
import moment from "moment";

import {
  API_VERIFICATION_MODE_CHANGE_NUMBER,
  CMS_PRIVACY_POLICY,
  CMS_TERMS_OF_SERVICES,
  ORDER_STATUS_ON_THE_WAY,
  NOTIFICATION_IDENTIFIER_ORDER_DETAIL,
  CARD_TYPE_VISA,
  CARD_TYPE_JCB,
  CARD_TYPE_DinersClub,
  CARD_TYPE_Discover,
  CARD_TYPE_AmericanExpress,
  CARD_TYPE_MasterCard
} from "../config/WebService";
import { Strings, Images } from "../theme";

import DataHandler from "../util/DataHandler";
import { request as logoutRequest } from "../actions/LogoutActions";

class Util {
  keyExtractor = (item, index) => index;
  getPlatform = () => Platform.OS;
  isPlatformAndroid = () => Platform.OS === "android";
  isEmailValide(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
  }
  isPlatformIOS() {
    return Platform.OS === "ios";
  }
  alert(message, alertType = "error") {
    MessageBarManager.showAlert({
      message,
      alertType
    });
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
  isItemExpensive(value) {
    return value >= DataHandler.expensiveItemCost();
  }
  getGmtDateTime(dateString, currentDateFormat, FormattedDateFormat) {
    return moment(dateString, currentDateFormat)
      .utc()
      .format(FormattedDateFormat);
  }
  validateFields = fieldsarray => {
    let isValid = true;
    let alreadySetFocus = false;
    for (let i = 0; i < fieldsarray.length; i += 1) {
      const setFocusOnInput = i === 0 || !alreadySetFocus;
      if (!fieldsarray[i].checkValidation(true, setFocusOnInput)) {
        isValid = false;
        alreadySetFocus = true;
      }
    }
    return isValid;
  };
  validatePassword = password => {
    if (password && password.length >= 8 && password.length <= 32) {
      return true;
    }
    return false;
  };
  validatePhone = phone => {
    const re = /^[0-9]{10}$/;
    return re.test(phone);
  };
  validateEmail = email => {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
  };
  validateNumeric = number => {
    const numberInput = Number(number);
    return numberInput > 0 && number.indexOf(".") === -1;
  };
  validateDecimal = number => {
    const numberInput = Number(number);
    return (
      numberInput > 0 &&
      !Number.isNaN(number) &&
      number.substr(number.length - 1) !== "."
    );
  };

  makeStringSingular(name, count, removeCount) {
    return count <= 1 ? name.substring(0, name.length - removeCount) : name;
  }

  getFormattedMobileNumber = (countryCode, mobileNo) => {
    return `${countryCode}-${mobileNo}`;
  };

  getCardImage = cardType => {
    let image = Images.cardPlaceholder;
    switch (cardType) {
      case CARD_TYPE_VISA:
        image = Images.cardVisa;
        break;
      case CARD_TYPE_JCB:
        image = Images.cardJcb;
        break;
      case CARD_TYPE_DinersClub:
        image = Images.cardDinersClub;
        break;
      case CARD_TYPE_Discover:
        image = Images.cardDiscover;
        break;
      case CARD_TYPE_AmericanExpress:
        image = Images.cardAmericanExpress;
        break;
      case CARD_TYPE_MasterCard:
        image = Images.cardMaster;
        break;
      default:
        image = Images.cardPlaceholder;
    }
    return image;
  };

  getLogId = (user, verification_mode) => {
    const mobile_number =
      verification_mode === API_VERIFICATION_MODE_CHANGE_NUMBER
        ? user.data.auth.new_mobile_no
        : user.data.auth.mobile_no;
    if (mobile_number.charAt(0) === "+") {
      return mobile_number;
    }
    return `+${mobile_number}`;
  };
  getRandomNumber() {
    let text = "";
    const possible =
      "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (let i = 0; i < 50; i += 1)
      text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
  }
  getFormattedPrice(price) {
    return `${DataHandler.currency()}${this.fixToDecimal(price)}`;
  }

  fixToDecimal = (number, decimal = 2) => +parseFloat(number).toFixed(decimal);

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
  getImageFromGalleryObject = (gallery, imageType = 0) => {
    let file;
    if (imageType === 0) {
      file = gallery ? gallery.thumb || gallery.file || undefined : undefined;
    } else {
      file = gallery
        ? gallery.mobile_file || gallery.file || undefined
        : undefined;
    }
    return file;
  };
  scrollToPosition = (scrollView, input) => {
    if (this.isPlatformAndroid()) {
      const scrollResponder = scrollView.getScrollResponder();
      scrollResponder.scrollResponderScrollNativeHandleToKeyboard(
        findNodeHandle(input),
        120,
        true
      );
    }
  };
  setSettings = settings => {
    if (settings.general_setting && settings.general_setting.currency) {
      DataHandler.setCurrency(settings.general_setting.currency);
    }
    if (
      settings.general_setting &&
      settings.general_setting.expensive_item_cost
    ) {
      DataHandler.setExpensiveItemCost(
        settings.general_setting.expensive_item_cost
      );
    }
  };

  logoutUser = (message = "") => {
    if (Actions.currentScene !== "login") {
      const store = DataHandler.getStore();
      const { user } = store.getState();
      const userId =
        user.data && user.data.entity_id ? user.data.entity_id : -1;
      const payload = {
        entity_id: userId,
        mobile_json: 1
      };
      DataHandler.getStore().dispatch(logoutRequest(payload, message));
    }
  };

  goToPrivacyPolicy = (isLogin: false) => {
    Actions.content({
      title: Strings.privacyPolicy,
      slug: CMS_PRIVACY_POLICY,
      isLogin
    });
  };
  goToTermsOfServices = (isLogin: false) => {
    Actions.content({
      title: Strings.termsOfServices,
      slug: CMS_TERMS_OF_SERVICES,
      isLogin
    });
  };

  handleNotificationNavigation = data => {
    const {
      order_status,
      order_id,
      entity_history_id,
      order_number,
      identifier
    } = data;

    if (
      order_status === ORDER_STATUS_ON_THE_WAY &&
      identifier !== NOTIFICATION_IDENTIFIER_ORDER_DETAIL
    ) {
      if (DataHandler.isTrackOrderCallbackIsSet()) {
        const { currentScene } = Actions;
        // send to track order screen
        if (currentScene !== "trackOrder") {
          Actions.popTo("trackOrder");
        }
        // call function of track order
        DataHandler.callBackTrackOrder(
          entity_history_id,
          order_id,
          order_number
        );
      } else {
        // track order
        Actions.trackOrder({
          orderId: order_id,
          title: `${order_number}`,
          historyId: entity_history_id
        });
      }
    } else if (DataHandler.isOderDetailCallbackIsSet()) {
      const { currentScene } = Actions;
      // send to order detail screen
      if (currentScene !== "orderDetail") {
        Actions.popTo("orderDetail");
      }
      // call function of order detail
      DataHandler.callBackOrderDetail(
        entity_history_id,
        order_id,
        order_number
      );
    } else {
      // send to order detail screen
      Actions.orderDetail({
        orderId: order_id,
        title: `${order_number}`,
        historyId: entity_history_id
      });
    }
  };
}

export default new Util();
