import React, { Component } from "react";

import {
  NativeModules,
  DeviceEventEmitter,
  AppState,
  Alert,
  Linking
} from "react-native";
import Utils from "../../util";
import Geolocation from "@react-native-community/geolocation";

const { GPSTracker } = NativeModules;

const LocationAccuracy = {
  enableHighAccuracy: true,
  timeout: 5000,
  maximumAge: 1000,
  distanceFilter: 1
};

const WithAppStateAndLocation = Comp => {
  return class WithAppStateAndLocation extends Component {
    constructor(props) {
      super(props);
      this.state = {
        tracking: false
      };
      this.clearLocationCheckTimer = this.clearLocationCheckTimer.bind(this);
    }

    static getDerivedStateFromProps(nextProps, prevState) {
      if (prevState.tracking !== nextProps.tracking) {
        return {
          tracking: nextProps.tracking
        };
      }

      // Return null to indicate no change to state.
      return null;
    }

    watchId = undefined;
    alertVisible = false;
    locationCheckTimer = undefined;

    componentDidMount() {
      this.updateLocation();
      this.subscribeToAppState();
      if (Utils.isPlatformAndroid()) {
        GPSTracker.onResume();
      }
      if (Utils.isPlatformAndroid())
        this.locationCheckTimer = setInterval(() => {
          if (!this.props.location.latitude) {
            GPSTracker.onResume();
          } else {
            this.clearLocationCheckTimer();
          }
        }, 2000);
    }

    subscribeToAppState = () =>
      AppState.addEventListener("change", this._handleAppStateChange);

    unsubscribeToAppState = () =>
      AppState.removeEventListener("change", this._handleAppStateChange);

    _handleAppStateChange = nextAppState => {
      if (nextAppState === "active") {
        if (Utils.isPlatformAndroid()) {
          //GPSTracker.onResume();
        } else {
          if (!this.state.tracking) this.updateLocation();
        }
      } else if (nextAppState === "background") {
        //if (Utils.isPlatformAndroid()) GPSTracker.onPause();
      }
    };

    componentWillUnmount() {
      // for ios only to stop location updates
      if (!Utils.isPlatformAndroid()) {
        Geolocation.clearWatch(this.watchId);
      } else {
        GPSTracker.onPause();
      }
      this.unsubscribeToAppState();
      this.clearLocationCheckTimer();
    }

    clearLocationCheckTimer() {
      if (this.locationCheckTimer) {
        clearInterval(this.locationCheckTimer);
        this.locationCheckTimer = undefined;
      }
    }

    setAlertVisible = () => (this.alertVisible = true);

    resetAlertVisible = () => (this.alertVisible = false);

    moveToLocationSettings = () =>
      Linking.canOpenURL("app-settings:")
        .then(supported => {
          return Linking.openURL("app-settings:");
        })
        .catch(err => {});

    showLocationAlert = () => {
      if (!this.alertVisible) {
        this.setAlertVisible();
        Alert.alert(
          "Location",
          "Your location is missing turn it on from settings",
          [
            {
              text: "OK",
              onPress: () => {
                this.resetAlertVisible();
                this.moveToLocationSettings();
              }
            }
          ],
          { cancelable: false }
        );
      }
    };

    updateUserCurrentLocation = location => {
      /* the only thing this class needs is the action to pass current location */
      /* which can be added to location reducer */
      if (this.props.currentLocation) this.props.currentLocation(location);
      if (!this.state.tracking) {
        this.props.orderDriverTrackingRequest({
          driver_id: this.props.user.entity_id,
          driver_location: [
            {
              latitude: location.latitude,
              longitude: location.longitude,
              timestamp: location.updateTime
            }
          ]
        });
      }
    };

    locationSuccess = position => {
      const location = {
        latitude: position.coords.latitude,
        longitude: position.coords.longitude,
        updateTime: position.timestamp
      };
      this.updateUserCurrentLocation(location);
    };

    locationFailure = error => {
      if (error && error.PERMISSION_DENIED && error.PERMISSION_DENIED === 1) {
        this.showLocationAlert();
      }
    };

    startTrackingService = () => {
      if (this.watchId) this.stopTrackingService();

      this.watchId = navigator.geolocation.watchPosition(
        this.locationSuccess,
        this.locationFailure,
        LocationAccuracy
      );
    };

    stopTrackingService = () => {
      navigator.geolocation.clearWatch(this.watchId);
      navigator.geolocation.stopObserving();
      this.watchId = undefined;
    };


    getPosition = () => {
      Geolocation.getCurrentPosition(
        this.locationSuccess,
        this.locationFailure,
        LocationAccuracy
      );
    };

    updateLocation() {
      if (Utils.isPlatformAndroid()) {
        // for android only
        DeviceEventEmitter.addListener(
          "updateLocationNotification",
          location => {
            this.updateUserCurrentLocation(location);
          }
        );
      } else {
        // for ios only
        this.state.tracking ? this.startTrackingService() : this.getPosition();
      }
    }

    render() {
      const { navigation, screenProps, ...rest } = this.props;
      return <Comp {...rest} />;
    }

    componentDidUpdate(prevProps, prevState) {
      // check for change in tracking service
      if (prevState.tracking !== this.state.tracking) {
        // if tracking true update service
        if (this.state.tracking) {
          this.updateLocation();
        } else {
          // if tracking false stop tracking service
          this.stopTrackingService();
          this.updateLocation();
        }
      }
    }
  };
};

export default WithAppStateAndLocation;
