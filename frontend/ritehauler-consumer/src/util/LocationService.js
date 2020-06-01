import { NativeModules, Alert } from "react-native";
import LocationSwitch from "react-native-location-switch";

import { LOCATION_TIME_OUT, LOCATION_HIGH_ACCURACY } from "../constants";
import Utils from "../util";

const { GPSTracker } = NativeModules;

class LocationService {
  successCallback = undefined;
  failureCallback = undefined;
  isRequestInProcess = false;

  getCurrentLocation(successCallback, failureCallback) {
    if (this.isRequestInProcess === false) {
      this.successCallback = successCallback;
      this.failureCallback = failureCallback;
      this._checkAndroidSettings();
      this.isRequestInProcess = true;
    }
  }

  _checkAndroidSettings = () => {
    if (Utils.isPlatformAndroid()) {
      GPSTracker.getCurrentPosition({
        title: "Location Permission Required",
        message:
          "Rite Hauler needs your location to provide better services at your doorstep. Enable location from settings",
        buttonText: "GO TO SETTINGS"
      })
        .then(result => {
          this.successCallback(result);
          this.isRequestInProcess = false;
        })
        .catch(error => {
          this.failureCallback(error);
          this.isRequestInProcess = false;
        });
    } else {
      this._geoLocation();
    }
  };

  _geoLocation = () => {
    navigator.geolocation.getCurrentPosition(
      position => {
        this.successCallback(position.coords);
        this.isRequestInProcess = false;
      },
      error => {
        Alert.alert(
          "Location Permission Required",
          "Rite Hauler needs your location to provide better services at your doorstep.  Enable location from settings",
          [
            {
              text: "Cancel",
              onPress: () => {
                this.failureCallback(error);
                this.isRequestInProcess = false;
              },
              style: "cancel"
            },
            {
              text: "GO TO SETTINGS",
              onPress: () => {
                LocationSwitch.enableLocationService(
                  1000,
                  true,
                  () => {
                    setTimeout(() => {
                      this._geoLocation();
                    }, 2000);
                  },
                  () => {
                    this.failureCallback(error);
                    this.isRequestInProcess = false;
                  }
                );
              }
            }
          ],
          { cancelable: false }
        );

        /*
        this.failureCallback(error);
        this.isRequestInProcess = false;
        */
      },
      { enableHighAccuracy: LOCATION_HIGH_ACCURACY, timeout: LOCATION_TIME_OUT }
    );
  };
}

export default new LocationService();
