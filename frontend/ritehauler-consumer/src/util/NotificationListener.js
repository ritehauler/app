import { Platform } from "react-native";
import firebase from "react-native-firebase";

import { Colors } from "../theme";
import { setNotificationInfo } from "../actions/NotificationListingActions";
import { NOTIFICATION_IDENTIFIER_CUSTOM } from "../config/WebService";
import DataHandler from "../util/DataHandler";
import Utils from "../util";

/* handle notifications */
function handleNotification(notification, appClosed, store) {
  if (notification.data.my_custom_data) {
    // get userid
    const { user } = store.getState();
    const userId = user.data && user.data.entity_id ? user.data.entity_id : -1;
    const customData = JSON.parse(notification.data.my_custom_data);

    if (
      customData.identifier !== NOTIFICATION_IDENTIFIER_CUSTOM &&
      customData.user_id === userId
    ) {
      if (!appClosed) {
        Utils.handleNotificationNavigation(customData);
      } else {
        DataHandler.setIsOpenFromNotification(true);
        Utils.handleNotificationNavigation(customData);
      }
    }
  }
}

export function registerFCMListener(store) {
  const chanelId = "ritehauler";

  /* Android Specific create channel */
  if (Platform.OS === "android") {
    const channel = new firebase.notifications.Android.Channel(
      chanelId,
      "Notification Channel",
      firebase.notifications.Android.Importance.Max
    ).setDescription("");
    firebase.notifications().android.createChannel(channel);
  }

  /* Notification came when app is in foreground */
  firebase.notifications().onNotification(notification => {
    // get userid
    const { user } = store.getState();
    const userId = user.data && user.data.entity_id ? user.data.entity_id : -1;

    if (notification.data.my_custom_data) {
      // update notification and order listing when notifcation comes
      const customData = JSON.parse(notification.data.my_custom_data);

      if (customData.user_id === userId) {
        // create notification object
        const localNotification = new firebase.notifications.Notification()
          .setNotificationId(notification.notificationId)
          .setTitle(notification.title)
          .setBody(notification.body)
          .setData(notification.data)
          .setSound("default");
        localNotification.ios = notification.ios;
        localNotification.android = notification.android;

        // android specific
        if (Platform.OS === "android") {
          localNotification.android.setChannelId(chanelId);
          localNotification.android.setAutoCancel(true);
          localNotification.android.setSmallIcon("icon_notification"); // name of the icon placed in android drawable or file name or url
          localNotification.android.setColor(Colors.accent);
        } else {
          localNotification.ios.setBadge(notification.ios.badge);
        }

        // display local notification
        firebase
          .notifications()
          .displayNotification(localNotification)
          .catch(err => {
            //console.log("displayNotification err", err);
          });

        if (customData.identifier !== NOTIFICATION_IDENTIFIER_CUSTOM) {
          store.dispatch(
            setNotificationInfo(
              customData.order_id,
              customData.order_number,
              new Date().getTime()
            )
          );
        }
      }
    }
  });

  /* Notification is tapped/opened in when app is in foreground or background */
  firebase
    .notifications()
    .onNotificationOpened((notificationOpen: NotificationOpen) => {
      handleNotification(notificationOpen.notification, false, store);
    });

  /* App is closed, and opened by notification */
  firebase
    .notifications()
    .getInitialNotification()
    .then((notificationOpen: NotificationOpen) => {
      if (notificationOpen) {
        handleNotification(notificationOpen.notification, true, store);
      }
    });
}

/* Get device token */
export function getToken() {
  return new Promise((resolve, reject) => {
    firebase
      .messaging()
      .getToken()
      .then(token => {
        resolve(token || "");
      })
      .catch(err => {
        resolve("");
        //reject(err);
      });
  });
}

export function removeAllNotifications() {
  firebase.notifications().cancelAllNotifications();
  firebase.notifications().removeAllDeliveredNotifications();
  if (Utils.isPlatformIOS()) {
    firebase.notifications().setBadge(0);
  }
}

/* check notification permission */
export function checkPermission() {
  firebase
    .messaging()
    .hasPermission()
    .then(enabled => {
      return enabled;
    });
}

/* Request permission for notifications */
export function requestPermission(permissionCallback = undefined) {
  if (Platform.OS === "ios") {
    firebase
      .messaging()
      .requestPermission()
      .then(() => {
        // User has authorised
        if (permissionCallback) {
          permissionCallback(true);
        }
      })
      .catch(error => {
        // User has rejected permissions
        if (permissionCallback) {
          permissionCallback(false);
        }
        // User has rejected permissions
        //console.log("requestPermission error", error);
      });
  }
}
