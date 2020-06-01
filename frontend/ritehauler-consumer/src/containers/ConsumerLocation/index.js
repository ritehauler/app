// @flow
import _ from "lodash";
import React, { Component } from "react";
import { View, Keyboard, DeviceEventEmitter } from "react-native";
import firebase from "react-native-firebase";
import { Actions } from "react-native-router-flux";
import PropTypes from "prop-types";

import Geocoder from "react-native-geocoder";
import { connect } from "react-redux";
import {
  PickUpLocation,
  DropOffLocation,
  GradientButton,
  LeftViewNavigation
} from "../../appComponents";
import LocationService from "../../util/LocationService";
import { userDataHelper } from "../../dataHelper";
import styles from "./styles";
import DataHandler from "../../util/DataHandler";
import { Images, Strings } from "../../theme";
import { TextWithState, BackHandler } from "../../components";
import CurrentLocationIcon from "./CurrentLocationIcon";
import MapPlaceHolder from "./MapPlaceHolder";
import MapViewLocation from "./MapViewLocation";
import CenterPinMap from "./CenterPinMap";
import CurrentLocationLoading from "./CurrentLocationLoading";

import {
  GOOGLE_API_KEY,
  CONFIRM_PICKUP_STATUS,
  ADD_DROPOFF_STATUS,
  CONFIRM_DROPOFF_STATUS,
  BOOK_STATUS,
  INITIAL_REGION_MAP
} from "../../constants";
import { updateOrderInfo } from "../../actions/OrderActions";
import { request as stateCityRequest } from "../../actions/StateCity";
import { request as generalSettingsRequest } from "../../actions/GeneralSettingsActions";
import { request as updateNotificationRequest } from "../../actions/UpdateTokenActions";
import Utils from "../../util";

class ConsumerLocation extends Component {
  static propTypes = {
    networkInfo: PropTypes.object.isRequired,
    stateCity: PropTypes.object.isRequired,
    updateOrderInfo: PropTypes.func.isRequired,
    stateCityRequest: PropTypes.func.isRequired,
    updateNotificationRequest: PropTypes.func.isRequired,
    generalSettingsRequest: PropTypes.func.isRequired,
    user: PropTypes.object.isRequired
  };

  /* ===================================================  component functions  ================================================================  */

  static onEnter = () => {
    if (DataHandler.isOpenFromNotification()) {
      DataHandler.setIsOpenFromNotification(false);
      DataHandler.callBackNotification();
    }
  };

  constructor(props) {
    super(props);
    this.onPressPickUp = this.onPressPickUp.bind(this);
    this.onPressDropOff = this.onPressDropOff.bind(this);
    this.onPressButton = this.onPressButton.bind(this);
    this.onGeoLocationReceived = this.onGeoLocationReceived.bind(this);
    this.onGeoLocationError = this.onGeoLocationError.bind(this);
    this.handleBackPress = this.handleBackPress.bind(this);
    this.onMapReady = this.onMapReady.bind(this);
    this.onRegionChange = this.onRegionChange.bind(this);
    this.onRegionChangeComplete = this.onRegionChangeComplete.bind(this);
    this.renderNavbarText = this.renderNavbarText.bind(this);
    this.renderNavbarImage = this.renderNavbarImage.bind(this);
    this.onPressRetry = this.onPressRetry.bind(this);
  }

  state = {
    pickUpLocation: "...",
    showLinePickUp: false,
    dropOffLocation: "",
    isDropOffCollapsed: true,
    pickUpCity: undefined,
    dropOffCity: undefined,
    hideAnimatableView: false,
    pickUpLatLong: undefined,
    dropOffLatLong: undefined,
    centerPinImage: undefined,
    centerPinText: undefined,
    lastCitySelected: {},
    centerPinLoading: true,
    centerPinDisable: false,
    pickUpLastSelectedLocation: undefined,
    dropOffLastSelectedLocation: undefined,
    onRetryPressCenterPin: undefined,
    status: CONFIRM_PICKUP_STATUS,
    locationResolved: false,
    isMapPlaceHolderShown: true,
    setPinPickUp: false,
    setPinDropOff: false,
    textButton: Strings.confirmPickUp,
    isButtonDisable: true,
    isMovingOnMap: false,
    isMovingDropOffMarker: false,
    isGettingAddress: false,
    backToPickUp: false,
    backToDropOff: false,
    currentRegion: INITIAL_REGION_MAP,
    hideCurrentLocationIcon: false,
    hideCurrentLocationLoading: true
  };

  componentWillMount() {
    Keyboard.dismiss();
  }

  componentDidMount() {
    // set na var
    Actions.refresh({
      left: this.renderNavbarImage,
      title: this.renderNavbarText
    });
    DataHandler.setCallBackNotification(
      this.getLocationAndSetNavBarAfterNotificationHandle
    );

    // send request to update city/state list
    this.props.stateCityRequest({ mobile_json: 1 });

    // send request to send general settings
    this.props.generalSettingsRequest();
    if (Utils.isPlatformAndroid()) {
      DeviceEventEmitter.addListener(
        "gettingLocationNotification",
        location => {
          this.setState({ hideCurrentLocationLoading: false });
        }
      );
    }

    // listener for token update
    this.onTokenRefreshListener = firebase
      .messaging()
      .onTokenRefresh(fcmToken => {
        this.sendUpdateTokenRequest();
        // Process your token as required
      });

    // check if token is empty in login
    this._checkPushNotificationTokenIsEmpty();
  }

  componentWillUnmount() {
    if (this.timerGetLocationNotificationHandle) {
      clearTimeout(this.timerGetLocationNotificationHandle);
    }
    if (this.timerMapReady) {
      clearTimeout(this.timerMapReady);
    }
    if (this.timerGeoLocationReceived) {
      clearTimeout(this.timerGeoLocationReceived);
    }
    if (this.timerFadeoutPlaceholder) {
      clearTimeout(this.timerFadeoutPlaceholder);
    }
    if (this.clearIdOnRegionChangeComplete) {
      clearTimeout(this.clearIdOnRegionChangeComplete);
    }
    if (this.timerCurrentLocationFailure) {
      clearTimeout(this.timerCurrentLocationFailure);
    }
    DeviceEventEmitter.removeListener("gettingLocationNotification", () => {});

    this.onTokenRefreshListener();
  }

  /* ===================================================  geo location functions  ================================================================  */

  geoCodeLocation(latitude, longitude) {
    if (this.props.networkInfo.isNetworkConnected) {
      if (this.state.onRetryPressCenterPin) {
        this.setState({
          onRetryPressCenterPin: undefined,
          centerPinText: undefined,
          centerPinImage: undefined,
          centerPinLoading: true
        });
      }

      this.state.isGettingAddress = true;
      Geocoder.fallbackToGoogle(GOOGLE_API_KEY);
      Geocoder.geocodePosition({
        lat: latitude,
        lng: longitude
      })
        .then(this.onGeoLocationReceived)
        .catch(err => {
          this.onGeoLocationError(Strings.errorMessageGeoLocation);
        });
    } else {
      this.onGeoLocationError(Strings.noInternetMessage);
    }
  }

  onGeoLocationReceived(res) {
    this.timerGeoLocationReceived = setTimeout(() => {
      this.state.isGettingAddress = false;
      if (this.checkMapIsNotMoving()) {
        if (res.length && res[0].locality) {
          const selectedCity = this.getFilterCities(res[0].locality);
          if (selectedCity) {
            this.handleStateWhenCitySelected(
              res[0].formattedAddress,
              selectedCity
            );
          } else {
            this.setStateDoesNotOperate();
          }
        } else {
          this.setStateDoesNotOperate();
        }
      }
      this.timerGeoLocationReceived = undefined;
    }, 1000);
  }

  onGeoLocationError(err) {
    this.setLatLongPickUpAndDropOff();
    this.setState({
      onRetryPressCenterPin: this.onPressRetry,
      centerPinImage: undefined,
      centerPinLoading: false,
      isGettingAddress: false,
      centerPinText: err,
      isButtonDisable: true
    });
  }

  handleStateWhenCitySelected = (address, city) => {
    if (this.state.status === CONFIRM_PICKUP_STATUS) {
      this.setStatePickUpLocationAddress(address, city);
    } else if (
      this.state.status === ADD_DROPOFF_STATUS ||
      this.state.status === CONFIRM_DROPOFF_STATUS
    ) {
      this.setStateDropOffLocationAddress(address, city);
    }
  };

  getFilterCities(searchText) {
    const cities = this.props.stateCity.data;
    let selectedCity;
    const len = cities.length;
    const pattern = new RegExp(searchText, "i");
    for (let i = 0; i < len; i += 1) {
      const { data } = cities[i];
      const citiesArray = _.filter(data, city => {
        return city.name.search(pattern) !== -1;
      });
      if (citiesArray.length > 0) {
        selectedCity = citiesArray[0];
        break;
      }
    }
    return selectedCity;
  }

  getLatLngText(latitude, longitude) {
    return `[${latitude.toFixed(4)},${longitude.toFixed(4)}]`;
  }

  onPressRetry() {
    this.geoCodeLocation(
      this.state.currentRegion.latitude,
      this.state.currentRegion.longitude
    );
  }

  /* ===================================================  click functions  ================================================================  */

  onPressButton() {
    if (this.checkMapIsNotMoving()) {
      if (this.state.status === CONFIRM_PICKUP_STATUS) {
        this.setStateAddDropOff();
      } else if (this.state.status === ADD_DROPOFF_STATUS) {
        this.onPressDropOff();
      } else if (this.state.status === CONFIRM_DROPOFF_STATUS) {
        this.setStateBook();
      } else if (this.state.status === BOOK_STATUS) {
        this.goToAddItem();
      }
    }
  }

  goToAddItem = () => {
    const data = {
      pickup: {
        address: this.state.pickUpLocation,
        latitude: `${this.state.pickUpLatLong.latitude}`,
        longitude: `${this.state.pickUpLatLong.longitude}`,
        city_id: this.state.pickUpCity.entity_id
      },
      dropoff: {
        address: this.state.dropOffLocation,
        latitude: `${this.state.dropOffLatLong.latitude}`,
        longitude: `${this.state.dropOffLatLong.longitude}`,
        city_id: this.state.dropOffCity.entity_id
      }
    };
    this.props.updateOrderInfo(data);
    Actions.addItem();
    DataHandler.setFirstItem(true);
    DataHandler.setCallBackOrderPlace(this.resetAfterOrderPalace);
    DataHandler.setCallBackOrderDiscard(this.setNavbarAfterDiscard);
  };

  setNavbarAfterDiscard = () => {
    this.leftNavBar.setActionAndImage(this.handleBackPress, Images.back);
    this.titleNavBar.setText(Strings.booking);
  };

  getLocationAndSetNavBarAfterNotificationHandle = () => {
    Actions.refresh({
      left: this.renderNavbarImage,
      title: this.renderNavbarText
    });

    this.timerGetLocationNotificationHandle = setTimeout(() => {
      this._getCurrentLocation();
    }, 500);
  };

  resetAfterOrderPalace = () => {
    this.setState({
      showLinePickUp: false,
      dropOffLocation: "",
      isDropOffCollapsed: true,
      dropOffCity: undefined,
      hideAnimatableView: false,
      pickUpLatLong: undefined,
      dropOffLatLong: undefined,
      centerPinImage: Images.pickUpMarker,
      centerPinText: undefined,
      centerPinLoading: false,
      centerPinDisable: false,
      dropOffLastSelectedLocation: undefined,
      onRetryPressCenterPin: undefined,
      status: CONFIRM_PICKUP_STATUS,
      textButton: Strings.confirmPickUp,
      hideCurrentLocationIcon: false
    });
    this.props.generalSettingsRequest();
  };

  onPressPickUp() {
    if (this.checkMapIsNotMoving()) {
      if (
        this.state.status === ADD_DROPOFF_STATUS ||
        this.state.status === CONFIRM_DROPOFF_STATUS
      ) {
        this.setStateConfirmPickupLocation();
      } else {
        this.selectLocationPickUp();
      }
    }
  }

  selectLocationPickUp = () => {
    const city = this.state.isButtonDisable
      ? this.state.lastCitySelected
      : this.state.pickUpCity || {};
    const locationLatLong =
      this.state.status === BOOK_STATUS
        ? this.state.pickUpLatLong
        : this.state.currentRegion || {};

    Actions.searchLocation({
      location: locationLatLong,
      city,
      updateNearByList: !this.state.isButtonDisable,
      callback: data => {
        if (!_.isEqual(data.location, this.state.pickUpLastSelectedLocation)) {
          if (this.state.status !== BOOK_STATUS) {
            this.setState(
              {
                pickUpLocation: data.name,
                pickUpCity: data.city,
                lastCitySelected: data.city,
                setPinPickUp: true,
                pickUpLastSelectedLocation: data.location,
                centerPinImage: Images.pickUpMarker,
                centerPinText: undefined,
                centerPinLoading: false,
                onRetryPressCenterPin: undefined,
                hideAnimatableView: true,
                isButtonDisable: false
              },
              () => this.mapViewLocation.animateToRegion(data.location)
            );
          } else {
            this.setState(
              {
                pickUpLocation: data.name,
                lastCitySelected: data.city,
                pickUpLastSelectedLocation: data.location,
                pickUpCity: data.city,
                pickUpLatLong: data.location
              },
              () => this.mapViewLocation.fitToCoordinates()
            );
          }
        }
      },
      onCitySelected: lastCity => {
        this.state.lastCitySelected = lastCity;
      }
    });
  };

  selectLocationDropOff = () => {
    const city =
      this.state.isButtonDisable || this.state.status === ADD_DROPOFF_STATUS
        ? this.state.lastCitySelected
        : this.state.dropOffCity || {};
    const locationLatLong =
      this.state.status === BOOK_STATUS
        ? this.state.dropOffLatLong
        : this.state.currentRegion || {};

    Actions.searchLocation({
      location: locationLatLong,
      city,
      updateNearByList: !this.state.isButtonDisable,
      callback: data => {
        if (!_.isEqual(data.location, this.state.dropOffLastSelectedLocation)) {
          if (this.state.status !== BOOK_STATUS) {
            this.setState(
              {
                dropOffLocation: data.name,
                dropOffCity: data.city,
                lastCitySelected: data.city,
                setPinDropOff: true,
                dropOffLastSelectedLocation: data.location,
                centerPinImage: Images.dropOffMarker,
                centerPinText: undefined,
                centerPinLoading: false,
                isButtonDisable: false,
                onRetryPressCenterPin: undefined,
                hideAnimatableView: true,
                textButton: Strings.confirmDropOff,
                status: CONFIRM_DROPOFF_STATUS
              },
              () => this.mapViewLocation.animateToRegion(data.location)
            );
          } else {
            this.setState(
              {
                dropOffLocation: data.name,
                lastCitySelected: data.city,
                dropOffCity: data.city,
                dropOffLastSelectedLocation: data.location,
                dropOffLatLong: data.location
              },
              () => this.mapViewLocation.fitToCoordinates()
            );
          }
        }
      },
      onCitySelected: lastCity => {
        this.state.lastCitySelected = lastCity;
      }
    });
  };

  onPressDropOff() {
    if (this.checkMapIsNotMoving()) {
      this.selectLocationDropOff();
    }
  }

  handleBackPress() {
    const { currentScene } = Actions;
    if (currentScene === "consumerLocation") {
      const handled = this.state.status !== CONFIRM_PICKUP_STATUS;
      if (
        this.state.status === ADD_DROPOFF_STATUS ||
        this.state.status === CONFIRM_DROPOFF_STATUS
      ) {
        this.setStateConfirmPickupLocation();
      } else if (this.state.status === BOOK_STATUS) {
        this.setStateConfirmDropOff();
      }

      return handled;
    }
    return false;
  }

  /* ===================================================  state changes functions  ================================================================  */

  setStateDropOffLocationAddress = (address, city) => {
    this.setState({
      dropOffLocation: address,
      dropOffCity: city,
      centerPinLoading: false,
      centerPinImage: Images.dropOffMarker,
      centerPinText: undefined,
      isButtonDisable: false
    });
  };

  setStatePickUpLocationAddress = (address, city) => {
    this.setState({
      pickUpLocation: address,
      pickUpCity: city,
      centerPinLoading: false,
      centerPinImage: Images.pickUpMarker,
      centerPinText: undefined,
      isButtonDisable: false
    });
  };

  setLatLongPickUpAndDropOff = () => {
    if (
      this.state.status === ADD_DROPOFF_STATUS ||
      this.state.status === CONFIRM_DROPOFF_STATUS
    ) {
      this.state.dropOffLocation = this.getLatLngText(
        this.state.currentRegion.latitude,
        this.state.currentRegion.longitude
      );
    } else {
      this.state.pickUpLocation = this.getLatLngText(
        this.state.currentRegion.latitude,
        this.state.currentRegion.longitude
      );
    }
  };

  setStateDoesNotOperate = () => {
    this.setLatLongPickUpAndDropOff();

    this.setState({
      centerPinImage: undefined,
      centerPinText: Strings.doesNotOperateMessage,
      centerPinLoading: false,
      isButtonDisable: true
    });
  };

  setStateBook = () => {
    this.titleNavBar.setText(Strings.booking);
    this.setState(
      {
        centerPinText: undefined,
        centerPinImage: undefined,
        centerPinLoading: false,
        dropOffLatLong: this.state.currentRegion,
        status: BOOK_STATUS,
        hideCurrentLocationIcon: true,
        textButton: Strings.continueButton,
        centerPinDisable: true
      },
      () => this.mapViewLocation.fitToCoordinates()
    );
  };

  setStateAddDropOff = () => {
    this.leftNavBar.setActionAndImage(this.handleBackPress, Images.back);
    this.titleNavBar.setText(Strings.selectDropoff);
    this.setState(
      {
        centerPinText: undefined,
        centerPinImage: Images.dropOffMarker,
        centerPinLoading: false,
        pickUpLatLong: this.state.currentRegion,
        dropOffLocation: "",
        isDropOffCollapsed: false,
        showLinePickUp: true,
        isMovingDropOffMarker: true,
        textButton: Strings.addDropOffLocation,
        status: ADD_DROPOFF_STATUS,
        centerPinDisable: true
      },
      () => {
        this.mapViewLocation.animateToRegion({
          latitude: this.state.currentRegion.latitude - 0.001,
          longitude: this.state.currentRegion.longitude
        });
      }
    );
  };

  setStateConfirmDropOff = () => {
    const { dropOffLatLong } = this.state;
    this.setState(
      {
        dropOffLatLong: undefined,
        textButton: Strings.confirmDropOff,
        status: CONFIRM_DROPOFF_STATUS,
        backToDropOff: true,
        centerPinDisable: true,
        centerPinImage: Images.dropOffMarker,
        hideCurrentLocationIcon: false,
        centerPinText: undefined,
        centerPinLoading: false
      },
      () => {
        this.titleNavBar.setText(Strings.selectDropoff);
        this.mapViewLocation.animateToRegion(dropOffLatLong);
      }
    );
  };

  setStateConfirmPickupLocation = () => {
    this.centerPinMap.zoomOutCenter(0);
    const { pickUpLatLong } = this.state;
    this.setState(
      {
        showLinePickUp: false,
        pickUpLatLong: undefined,
        textButton: Strings.confirmPickUp,
        status: CONFIRM_PICKUP_STATUS,
        onRetryPressCenterPin: undefined,
        isButtonDisable: false,
        centerPinText: undefined,
        isDropOffCollapsed: true,
        backToPickUp: true,
        centerPinImage: Images.pickUpMarker,
        hideAnimatableView: true
      },
      () => {
        this.leftNavBar.setActionAndImage(Actions.drawerOpen, Images.drawer);
        this.titleNavBar.setText(Strings.selectPickUp);
        this.mapViewLocation.animateToRegion(pickUpLatLong);
      }
    );
  };

  fadeoutPlaceholderAndGetLocation = region => {
    this.mapViewPlaceholder.fadeOut();
    this.timerFadeoutPlaceholder = setTimeout(() => {
      this.state.currentRegion = region;
      this.state.isMapPlaceHolderShown = false;
      this.geoCodeLocation(region.latitude, region.longitude);
    }, 1000);
  };

  handleOnRegionComplete = region => {
    this.state.currentRegion = region;
    const {
      isMovingDropOffMarker,
      backToPickUp,
      backToDropOff,
      setPinPickUp,
      setPinDropOff
    } = this.state;
    if (setPinPickUp) {
      this.setState(
        {
          setPinPickUp: false,
          hideAnimatableView: false
        },
        () => this.centerPinMap.zoomInCenter()
      );
    } else if (setPinDropOff) {
      this.setState(
        {
          setPinDropOff: false,
          hideAnimatableView: false
        },
        () => this.centerPinMap.zoomInCenter()
      );
    }
    // move because back to drop off
    else if (backToDropOff) {
      this.setState({
        backToDropOff: false,
        centerPinDisable: false
      });
    } else if (backToPickUp) {
      // move to because back to pick up
      this.setState({ hideAnimatableView: false, backToPickUp: false }, () =>
        this.centerPinMap.zoomInCenter()
      );
    } else if (isMovingDropOffMarker) {
      // if moving to set add pickup
      this.setState({
        isMovingDropOffMarker: false,
        centerPinDisable: false
      });
    } else {
      // set isMovingOnMap and then check after 400 millseconds if not started again

      this.state.isMovingOnMap = false;
      this.clearIdOnRegionChangeComplete = setTimeout(() => {
        this.setState(
          {
            isMovingOnMap: false,
            centerPinText: undefined,
            centerPinImage: undefined,
            centerPinLoading: true,
            hideAnimatableView: false,
            isMovingDropOffMarker: false
          },
          () => {
            this.centerPinMap.zoomInCenter();
            this.clearIdOnRegionChangeComplete = undefined;
            if (this.state.status === ADD_DROPOFF_STATUS) {
              this.state.textButton = Strings.confirmDropOff;
              this.state.status = CONFIRM_DROPOFF_STATUS;
            }
            this.geoCodeLocation(region.latitude, region.longitude);
          }
        );
      }, 400);
    }
  };

  /* ===================================================  location funcions ================================================================  */

  _getCurrentLocation = () => {
    // get current location
    LocationService.getCurrentLocation(
      this._onCurrentLocationSuccess,
      this._onCurrentLocationFailure
    );
  };

  _onCurrentLocationSuccess = coords => {
    // set lat,long to current location
    if (this.state.locationResolved === false) {
      this.setState({
        locationResolved: true,
        hideCurrentLocationLoading: true,
        pickUpLocation: this.getLatLngText(coords.latitude, coords.longitude)
      });

      if (!this.props.networkInfo.isNetworkConnected) {
        this.fadeoutPlaceholderAndGetLocation(coords);
      }
    } else {
      this.setState({
        hideCurrentLocationLoading: true
      });
    }

    // animate to region
    this.mapViewLocation.animateToRegion(coords);
  };

  _onCurrentLocationFailure = error => {
    // if it is not android show error message
    this.state.hideCurrentLocationLoading = true;

    if (this.state.locationResolved === false) {
      // set status not operatable
      this.setStateDoesNotOperate();

      // fade out placeholder
      this.mapViewPlaceholder.fadeOut();

      // make is ready first time
      this.timerCurrentLocationFailure = setTimeout(() => {
        this.state.locationResolved = true;
        this.state.isMapPlaceHolderShown = false;
      }, 1000);
    }
  };

  /* ===================================================  map functions ================================================================  */

  onMapReady() {
    // on map ready get current location
    this.timerMapReady = setTimeout(() => {
      if (!DataHandler.isOpenFromNotification()) {
        this._getCurrentLocation();
      }
    }, 500);
  }

  onRegionChange(region) {
    this.state.isGettingAddress = false;
    // check if map is not moving
    if (this.checkMapIsNotMoving() && this.state.status !== BOOK_STATUS) {
      // set state map is moving
      this.state.isMovingOnMap = true;

      if (this.timerGeoLocationReceived) {
        clearTimeout(this.timerGeoLocationReceived);
      }

      // move start again remove clearIdOnRegionChangeComplete
      // zoom out when map move start
      //this.centerPinMap.zoomOutCenter();

      if (this.clearIdOnRegionChangeComplete) {
        clearTimeout(this.clearIdOnRegionChangeComplete);
      } else {
        // zoom out when map move start
        this.centerPinMap.zoomOutCenter();
      }
    }
  }

  checkMapIsNotMoving = () => {
    const isMapMoving =
      !this.state.isMapPlaceHolderShown &&
      !this.state.isMovingOnMap &&
      !this.state.isMovingDropOffMarker &&
      !this.state.backToPickUp &&
      !this.state.setPinPickUp &&
      !this.state.setPinDropOff &&
      !this.state.isGettingAddress &&
      !this.state.backToDropOff;
    return isMapMoving;
  };

  onRegionChangeComplete(region) {
    if (this.state.locationResolved) {
      if (this.state.isMapPlaceHolderShown) {
        this.fadeoutPlaceholderAndGetLocation(region);
      } else if (this.state.status !== BOOK_STATUS) {
        this.handleOnRegionComplete(region);
      }
    }
  }

  clearIdOnRegionChangeComplete = undefined;
  timerGeoLocationReceived = undefined;

  _checkPushNotificationTokenIsEmpty() {
    const { user } = this.props;
    if (!userDataHelper.checkHasNotificationToken(user)) {
      this.sendUpdateTokenRequest();
    }
  }

  sendUpdateTokenRequest() {
    const { user } = this.props;
    const payload = {
      entity_id: user.entity_id,
      device_type: Utils.getPlatform(),
      mobile_json: 1
    };
    this.props.updateNotificationRequest(payload);
  }

  /* ===================================================  render functions ================================================================  */

  renderNavbarText() {
    return (
      <TextWithState
        ref={ref => {
          this.titleNavBar = ref;
        }}
        style={styles.title}
      >
        {Strings.selectPickUp}
      </TextWithState>
    );
  }

  renderNavbarImage() {
    return (
      <LeftViewNavigation
        ref={ref => {
          this.leftNavBar = ref;
        }}
        image={Images.drawer}
        action={Actions.drawerOpen}
      />
    );
  }

  _renderCenterPin() {
    const {
      centerPinImage,
      centerPinText,
      centerPinLoading,
      centerPinDisable,
      onRetryPressCenterPin,
      hideAnimatableView
    } = this.state;

    return (
      <CenterPinMap
        ref={ref => {
          this.centerPinMap = ref;
        }}
        image={centerPinImage}
        text={centerPinText}
        isLoading={centerPinLoading}
        disable={centerPinDisable}
        onRetryPress={onRetryPressCenterPin}
        hideAnimatableView={hideAnimatableView}
      />
    );
  }

  _renderMap() {
    const { pickUpLatLong, dropOffLatLong } = this.state;
    return (
      <MapViewLocation
        ref={ref => {
          this.mapViewLocation = ref;
        }}
        onMapReady={this.onMapReady}
        onRegionChange={this.onRegionChange}
        onRegionChangeComplete={this.onRegionChangeComplete}
        pickUpLatLong={pickUpLatLong}
        dropOffLatLong={dropOffLatLong}
      />
    );
  }

  _renderCurrentLocationIcon() {
    return (
      <CurrentLocationIcon
        onPress={this._getCurrentLocation}
        ref={ref => {
          this.currentLocationIcon = ref;
        }}
        hide={this.state.hideCurrentLocationIcon}
      />
    );
  }

  _renderMapPlaceholder() {
    return (
      <MapPlaceHolder
        ref={ref => {
          this.mapViewPlaceholder = ref;
        }}
      />
    );
  }

  _renderPickAndDropLocationsBox() {
    return (
      <View style={styles.box}>
        {this._renderPickup()}
        {this._renderDropOff()}
      </View>
    );
  }

  _renderGradientButton() {
    const { textButton, isButtonDisable } = this.state;

    return (
      <GradientButton
        onPress={this.onPressButton}
        text={textButton}
        isDisable={isButtonDisable}
      />
    );
  }

  _renderDropOff() {
    const { dropOffLocation, isDropOffCollapsed } = this.state;
    return (
      <DropOffLocation
        text={dropOffLocation}
        collapsed={isDropOffCollapsed}
        numberOfLines={1}
        onPress={this.onPressDropOff}
        isCollapsible
      />
    );
  }

  _renderPickup() {
    const { pickUpLocation, showLinePickUp } = this.state;
    return (
      <PickUpLocation
        text={pickUpLocation}
        showLine={showLinePickUp}
        numberOfLines={1}
        onPress={this.onPressPickUp}
      />
    );
  }

  _renderMapContainer() {
    return (
      <View style={styles.mapContainer}>
        {this._renderMap()}
        {this._renderCenterPin()}
        {this._renderMapPlaceholder()}
        {this._renderPickAndDropLocationsBox()}
        {this._renderCurrentLocationIcon()}
      </View>
    );
  }

  _renderCurrentLocationLoading() {
    const { hideCurrentLocationLoading } = this.state;
    return <CurrentLocationLoading hide={hideCurrentLocationLoading} />;
  }

  render() {
    return (
      <View style={styles.container}>
        {this._renderMapContainer()}
        {this._renderGradientButton()}
        {this._renderCurrentLocationLoading()}
        <BackHandler onBackPress={this.handleBackPress} />
      </View>
    );
  }
}

const actions = {
  stateCityRequest,
  generalSettingsRequest,
  updateOrderInfo,
  updateNotificationRequest
};
const mapStateToProps = store => ({
  stateCity: store.stateCity,
  user: store.user.data,
  networkInfo: store.networkInfo
});

export default connect(
  mapStateToProps,
  actions
)(ConsumerLocation);
