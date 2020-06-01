// @flow
import { connect } from "react-redux";
import React, { Component } from "react";
import { Marker, Polyline } from "react-native-maps";
import _ from "lodash";
import {
  View,
  Animated,
  Image,
  TouchableWithoutFeedback,
  Text,
  ActivityIndicator
} from "react-native";
import { Images, ApplicationStyles } from "../../../theme";
import { Map, ButtonView } from "../../../components";
import DetailCardHandler from "./DetailCardHandler";
import { Metrics, Colors } from "../../../theme";
import styles from "./styles";
import Utils from "../../../util";
import {
  GOOGLE_API_KEY,
  API_GOOGLE_DIRECTION
} from "../../../config/WebService";
import {
  MAP_DELTAS,
  EDGE_PADDING,
  ENTITY_TYPE_ID_UPDATE_ORDER_STATUS,
  USER_ENTITY_TYPE_ID,
  MAX_ZOOM_LEVEL,
  ABOUT_TO_REACH,
  ON_THE_WAY,
  RIDE_MODE
} from "../../../constant";
import { request as mapRouteRequest } from "../../../actions/MapRouteActions";
import { request as todaysOrdersRequest } from "../../../actions/TodaysOrdersActions";
import { request as updateAssignedOrderStatusRequest } from "../../../actions/UpdateAssignedOrderStatus";
import { showCard, hideCard } from "../../../actions/DetailCardActions";

// redux imports
import {
  setTrackingStatus,
  startBackgroundLocationService
} from "../../../actions/LocationActions";
import {
  selectorTracking,
  selectorLocation,
  selectCachedLoginUser,
  selectMarkers,
  selectPolylineCoordinates
} from "../../../reducers/reduxSelectors";
import { focusMap } from "../../../actions/HandleMapActions";

class OrderMapView extends Component {
  constructor(props) {
    super(props);
    Utils.setLoginUserID(props.user.data.entity_id);
    this.animationValue = new Animated.Value(0);
    this.state = {
      showDetailCard: false,
      selectedMarker: undefined,
      selectedMarkerIndex: undefined,
      status: 1,
      buttonTitle: "Arrived",
      initialRegion: {
        latitude: 41.850033,
        longitude: -87.6500523,
        ...MAP_DELTAS
      }
    };
    this.onMarkerPress = this.onMarkerPress.bind(this);
    this.onMapPress = this.onMapPress.bind(this);
  }

  static getDerivedStateFromProps(nextProps, prevState) {
    if (
      nextProps.location.latitude &&
      prevState.initialRegion.latitude !== nextProps.location.latitude
    ) {
      const initialRegion = { ...nextProps.location, ...MAP_DELTAS };
      return {
        initialRegion
      };
    }

    // Return null to indicate no change to state.
    return null;
  }

  componentDidMount() {
    Animated.timing(this.animationValue, {
      toValue: 1,
      duration: 600,
      useNativeDriver: true
    }).start();

    this.props.todaysOrdersRequest({
      driver_id: this.props.user.data.entity_id,
      hook: "order_pickup,order_dropoff",
      detail_key: "customer_id"
    });
  }

  shouldComponentUpdate(nextProps, nextState) {
    return (
      !_.isEqual(nextProps, this.props) || !_.isEqual(nextState, this.state)
    );
  }

  renderOffDutyIndicator() {
    if (!this.props.user.data.on_duty) {
      return (
        <View
          style={{
            width: Metrics.screenWidth,
            padding: Metrics.baseMargin,
            backgroundColor: Colors.background.primary,
            alignItems: "center",
            justifyContent: "center",
            position: "absolute",
            top: 0,
            elevation: 1.5
          }}
        >
          <Text style={ApplicationStyles.dBold16}>
            Turn duty toggle on to receive new orders
          </Text>
        </View>
      );
    }
    return null;
  }

  renderMapView() {
    const markers = this.renderMarkers();
    return (
      <Map
        initialRegion={this.state.initialRegion}
        ref={ref => {
          this.mapRef = ref;
        }}
        moveOnMarkerPress={false}
        maxZoomLevel={MAX_ZOOM_LEVEL}
        onPress={this.onMapPress}
      >
        {this.renderMarkers()}
        {this.renderCurrentLocationMarker()}
        {this.renderDestinationMarker()}
        {this.renderPolyline()}
      </Map>
    );
  }

  onMapPress() {
    this.props.hideCard();
  }

  renderMarkers() {
    const { markers } = this.props;
    return markers.length ? (
      <Marker
        stopPropagation={true}
        key={markers[0].order_pickup.latitude}
        coordinate={markers[0].order_pickup}
        image={Images.markerHomeLocation}
        onPress={() => this.onMarkerPress(0)}
        index={0}
      />
    ) : null;
  }

  onMarkerPress(index) {
    const { markers } = this.props;
    this.props.showCard();
    this.fitToCoordinates([markers[index].order_pickup]);
  }

  renderCurrentLocationMarker() {
    if (
      this.props.orderStatus === ON_THE_WAY ||
      this.props.orderStatus === ABOUT_TO_REACH
    ) {
      if (this.props.location && this.props.location.latitude) {
        return (
          <Marker
            coordinate={{
              latitude: this.props.location.latitude,
              longitude: this.props.location.longitude
            }}
            image={Images.markerTruckLocation}
          />
        );
        return null;
      }
    }

    return null;
  }

  renderDestinationMarker() {
    if (
      this.props.orderStatus === ON_THE_WAY ||
      this.props.orderStatus === ABOUT_TO_REACH
    ) {
      const { markers } = this.props;
      return (
        <Marker
          coordinate={{
            latitude: markers[0].order_dropoff.latitude,
            longitude: markers[0].order_dropoff.longitude
          }}
          image={Images.markerDeliveryLocation}
        />
      );
    }
    return null;
  }

  renderPolyline() {
    if (
      this.props.orderStatus === ON_THE_WAY ||
      this.props.orderStatus === ABOUT_TO_REACH
    ) {
      if (this.props.polyline && this.props.polyline.length) {
        return (
          <Polyline
            coordinates={this.props.polyline}
            strokeColor={Colors.accent2} // fallback for when `strokeColors` is not supported by the map-provider
            strokeWidth={Metrics.routeStrokeWidth}
          />
        );
      }
    }
    return null;
  }

  fitToCoordinates = coordinates =>
    this.mapRef.getMapViewRef().fitToCoordinates(coordinates, {
      animated: true,
      edgePadding: EDGE_PADDING
    });

  renderCurrentLocationButton() {
    return (
      <TouchableWithoutFeedback
        onPress={() => {
          //this.props.setTrackingStatus(!this.props.tracking);
          //this.props.startBackgroundLocationService();
          if (this.props.location.latitude) this.moveMapToCurrentLocation();
        }}
      >
        <View
          style={{
            position: "absolute",
            right: Metrics.baseMargin,
            bottom: Metrics.doubleBaseMargin
          }}
        >
          <Image source={Images.currentLocation} />
        </View>
      </TouchableWithoutFeedback>
    );
  }

  moveMapToCurrentLocation = () =>
    this.mapRef
      .getMapViewRef()
      .animateToRegion({ ...this.props.location, ...MAP_DELTAS });

  renderDetailCard() {
    return <DetailCardHandler />;
  }

  renderNoOrder() {
    if (!this.props.markers.length && !this.props.isFetching)
      return (
        <View
          style={{
            padding: Metrics.baseMargin,
            borderRadius: Metrics.smallMargin,
            backgroundColor: "white",
            position: "absolute",
            top: Metrics.screenHeight / 2.2
          }}
        >
          <Text>No orders found for today</Text>
        </View>
      );

    return null;
  }

  renderActivityIndicator() {
    if (this.props.isFetching)
      return (
        <View style={styles.activityIndicatorWrapper}>
          <ActivityIndicator size="small" color={Colors.accent} />
        </View>
      );

    return null;
  }

  render() {
    return (
      <Animated.View
        style={[styles.containerStyle, { opacity: this.animationValue }]}
      >
        {this.renderMapView()}
        {this.renderCurrentLocationButton()}
        {this.renderDetailCard()}
        {this.renderOffDutyIndicator()}
        {this.renderNoOrder()}
        {this.renderActivityIndicator()}
      </Animated.View>
    );
  }

  componentDidUpdate(prevProps, prevState) {
    if (this.props.handleMap.focusMap && this.props.markers.length) {
      this.mapRef.getMapViewRef().fitToElements(true);
      this.props.focusMap(false);
    }

    if (
      !this.props.mapRoute.length &&
      this.props.orderStatus === ON_THE_WAY &&
      this.props.markers.length &&
      this.props.location &&
      this.props.location.latitude
    ) {
      this.props.mapRouteRequest(API_GOOGLE_DIRECTION, {
        key: GOOGLE_API_KEY,
        mode: RIDE_MODE.DRIVING,
        origin: Utils.getLocationString(this.props.markers[0].order_pickup),
        destination: Utils.getLocationString(
          this.props.markers[0].order_dropoff
        ),
        waypoints: Utils.getLocationString({
          latitude: this.props.location.latitude,
          longitude: this.props.location.longitude
        })
      });

      this.props.startBackgroundLocationService();
      this.props.setTrackingStatus(true);
    }
  }
}

const mapStateToProps = state => {
  const user = { ...state.user, data: selectCachedLoginUser(state.user.data) };

  return {
    user: user,
    isFetching: state.todaysOrders.isFetching,
    orderStatus: state.todaysOrders.data.length
      ? state.todaysOrders.data[0].order_status.value
      : undefined,
    markers: selectMarkers(state.todaysOrders.data),
    location: selectorLocation(state.location),
    handleMap: state.handleMap,
    polyline: state.mapRoute.data.routes
      ? selectPolylineCoordinates(state.mapRoute.data)
      : undefined,
    mapRoute: state.mapRoute.data
  };
};

const actions = {
  setTrackingStatus,
  startBackgroundLocationService,
  updateAssignedOrderStatusRequest,
  showCard,
  hideCard,
  focusMap,
  todaysOrdersRequest,
  mapRouteRequest
};

export default connect(mapStateToProps, actions)(OrderMapView);
