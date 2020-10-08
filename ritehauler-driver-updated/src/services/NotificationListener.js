import { Platform } from "react-native";
import firebase from "react-native-firebase";
import { Actions } from "react-native-router-flux";
import moment from "moment";
import { Colors } from "../theme";
import Utils from "../util";
import { DATE_FORMAT } from "../constant";
/* handle notifications */
function handleNotification(
  notification,
  appClosed,
  dispatch,
  requestAssignedOrders
) {
  setTimeout(() => {
    if (notification._data && notification._data.my_custom_data) {
      try {
        const data = JSON.parse(notification._data.my_custom_data);
        if (
          data.identifier &&
          data.identifier === "order_reminder" &&
          data.order_id
        ) {
          Actions.orderSummary({
            orderEntityID: data.order_id
          });
        } else {
          if (Utils.getLoginUserID()) {
            dispatch(
              requestAssignedOrders({
                driver_id: Utils.getLoginUserID(),
                pickup_date: moment().format(DATE_FORMAT),
                hook: "order_pickup,order_dropoff",
                detail_key: "customer_id"
              })
            );
          }
        }
      } catch (e) {}
    }
  }, 1000);
}

export function registerFCMListener(dispatch, requestAssignedOrders) {
  const chanelId = "ritehaulerdriver";

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
    // create notification object
    const localNotification = new firebase.notifications.Notification()
      .setNotificationId(notification.notificationId)
      .setTitle(notification.title)
      .setBody(notification.body)
      .setData(notification.data)
      .setSound("default");
    localNotification.ios = notification.ios;
    localNotification.android = notification.android;

    /* android specific */
    if (Platform.OS === "android") {
      localNotification.android.setChannelId(chanelId);
      localNotification.android.setAutoCancel(true);
      localNotification.android.setSmallIcon("notification_icon"); // name of the icon placed in android drawable or file name or url
      localNotification.android.setColor(Colors.accent);
    }

    /* display local notification */
    firebase
      .notifications()
      .displayNotification(localNotification)
      .catch(err => {});
  });

  /* Notification is tapped/opened in when app is in foreground or background */
  firebase.notifications().onNotificationOpened(notificationOpen => {
    handleNotification(
      notificationOpen.notification,
      false,
      dispatch,
      requestAssignedOrders
    );
  });

  /* App is closed, and opened by notification */
  firebase
    .notifications()
    .getInitialNotification()
    .then(notificationOpen => {
      if (notificationOpen) {
        handleNotification(
          notificationOpen.notification,
          true,
          dispatch,
          requestAssignedOrders
        );
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
        reject(err);
      });
  });
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
      });
  }
}
