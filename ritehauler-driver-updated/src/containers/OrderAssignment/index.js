// @flow
import { connect } from "react-redux";
import React, { Component } from "react";
import { Marker, Polyline } from "react-native-maps";
import _ from "lodash";
import moment from "moment";
import { Actions } from "react-native-router-flux";
import {
  View,
  Animated,
  Image,
  TouchableWithoutFeedback,
  Text,
  ActivityIndicator
} from "react-native";
import { Images, Metrics, Colors } from "../../theme";
import { Map } from "../../components";
import DetailCard from "./DetailCard";
import Timer from "./Timer";
import styles from "./styles";
import Utils from "../../util";
import DeclineReason from "../VerifyItems/DeclineReason";
import OrderAutoDeclined from "./OrderAutoDeclined";
import Modal from "../Modal";
import { GOOGLE_API_KEY, API_GOOGLE_DIRECTION } from "../../config/WebService";
import {
  MAP_DELTAS,
  EDGE_PADDING,
  ENTITY_TYPE_ID_UPDATE_ORDER_STATUS,
  USER_ENTITY_TYPE_ID,
  MAX_ZOOM_LEVEL,
  ABOUT_TO_REACH,
  ON_THE_WAY,
  RIDE_MODE,
  AUTO_DECLINE_PERIOD
} from "../../constant";
import WithLoader from "../HOC/WithLoader";
// redux imports
import { request as mapRouteRequest } from "../../actions/MapRouteActions";
import { request as updateAssignedOrderStatusRequest } from "../../actions/UpdateAssignedOrderStatus";
import { showCard, hideCard } from "../../actions/DetailCardActions";
import { clearAssignedOrders } from "../../actions/AssignedOrders";

import {
  selectorTracking,
  selectorLocation,
  selectCachedLoginUser,
  selectMarkers,
  selectPolylineCoordinates,
  calculateDeclineTime
} from "../../reducers/reduxSelectors";

class OrderAssignment extends Component {
  constructor(props) {
    super(props);
    this.onMarkerPress = this.onMarkerPress.bind(this);
    this.onMapPress = this.onMapPress.bind(this);

    this.state = {
      initialRegion: {
        latitude: 41.850033,
        longitude: -87.6500523,
        ...MAP_DELTAS
      },
      isDetailCardVisible: false
    };
    this.cbOnDeclinePress = this.cbOnDeclinePress.bind(this);
    this.cbOnDetailCardPress = this.cbOnDetailCardPress.bind(this);
    this.showHideModal = this.showHideModal.bind(this);
    this.cbOnDeclineModalDonePress = this.cbOnDeclineModalDonePress.bind(this);
  }

  timeoutID = undefined;

  componentDidMount() {
    if (this.props.orderValidity.isOrderValid) {
      this.timeoutID = setTimeout(() => {
        this.mapsRef.getMapViewRef().fitToElements(true);
      }, 1000);
      this.props.mapRouteRequest(API_GOOGLE_DIRECTION, {
        key: GOOGLE_API_KEY,
        mode: RIDE_MODE.DRIVING,
        origin: Utils.getLocationString(this.props.markers[0].order_pickup),
        destination: Utils.getLocationString(
          this.props.markers[0].order_dropoff
        )
      });
    }
  }

  componentWillUnmount() {
    if (this.timeoutID) {
      clearTimeout(this.timeoutID);
      this.timeoutID = undefined;
    }
    this.props.clearAssignedOrders();
  }

  renderTimer() {
    return (
      <Timer
        ref={reference => {
          if (reference) {
            this.timer = reference.getWrappedInstance();
          }
        }}
        time={this.props.orderValidity.timeDifference}
        orderEntityID={this.props.orderEntityID}
        userEntityID={this.props.user.data.entity_id}
        cbOnDeclinePress={this.cbOnDeclinePress}
      />
    );
  }

  cbOnDeclinePress() {
    this.showHideModal(true);
  }

  renderMapView() {
    const markers = this.renderMarkers();
    return (
      <Map
        initialRegion={this.state.initialRegion}
        ref={ref => {
          this.mapsRef = ref;
        }}
        moveOnMarkerPress={false}
        maxZoomLevel={MAX_ZOOM_LEVEL}
        onPress={this.onMapPress}
      >
        {this.renderMarkers()}
        {this.renderPolyline()}
      </Map>
    );
  }

  onMapPress() {
    if (this.state.isDetailCardVisible) {
      this.setState({
        isDetailCardVisible: false
      });
    }
  }

  renderMarkers() {
    const { markers } = this.props;
    if (markers && markers.length) {
      return [
        <Marker
          stopPropagation={true}
          key={markers[0].order_pickup.latitude}
          coordinate={markers[0].order_pickup}
          image={Images.markerHomeLocation}
          onPress={() => this.onMarkerPress(0)}
          index={0}
        />,
        <Marker
          stopPropagation={true}
          key={markers[0].order_dropoff.latitude}
          coordinate={markers[0].order_dropoff}
          image={Images.markerDeliveryLocation}
          onPress={() => {}}
          index={0}
        />
      ];
    }
    return null;
  }

  onMarkerPress(index) {
    this.setState({
      isDetailCardVisible: true
    });
  }

  renderPolyline() {
    if (this.props.polyline && this.props.polyline.length) {
      return (
        <Polyline
          coordinates={this.props.polyline}
          strokeColor={Colors.accent2} // fallback for when `strokeColors` is not supported by the map-provider
          strokeWidth={Metrics.routeStrokeWidth}
        />
      );
    }
    return null;
  }

  fitToCoordinates = coordinates =>
    this.mapsRef.getMapViewRef().fitToCoordinates(coordinates, {
      animated: true,
      edgePadding: EDGE_PADDING
    });

  renderDetailCard() {
    if (this.state.isDetailCardVisible) {
      const {
        name,
        image,
        order_dropoff,
        order_pickup
      } = this.props.markers[0];

      return (
        <DetailCard
          user={{
            image,
            name,
            distance: Utils.getDistanceInMiles(
              parseFloat(order_pickup.latitude),
              parseFloat(order_pickup.longitude),
              this.props.location.latitude,
              this.props.location.longitude
            )
          }}
          dropLocation={order_dropoff}
          pickLocation={order_pickup}
          cbOnDetailCardPress={this.cbOnDetailCardPress}
        />
      );
    }
  }

  cbOnDetailCardPress() {
    Actions.orderSummary({
      orderEntityID: this.props.orderEntityID,
      isAcceptable: true
    });
  }

  cbOnDeclineModalDonePress(declineReason) {
    this.showHideModal(false);
    this.timer.autoDeclineOrder(declineReason);
  }

  showHideModal(visibility) {
    this.declineModal.setModalVisible(visibility);
  }

  render() {
    return (
      <View style={styles.containerStyle}>
        {this.renderTimer()}
        <View style={styles.containerStyle}>
          {this.renderMapView()}
          {this.renderDetailCard()}
        </View>
        <Modal ref={ref => (this.declineModal = ref)}>
          <DeclineReason cbOnDonePress={this.cbOnDeclineModalDonePress} />
        </Modal>
      </View>
    );
  }
  componentDidUpdate(prevProps) {
    if (
      this.props.markers.length &&
      !_.isEqual(prevProps.markers, this.props.markers)
    ) {
      this.mapsRef.getMapViewRef().fitToElements(true);
      this.timer.restartTimer(this.props.orderValidity.timeDifference);
    }
  }
}

const mapStateToProps = ({
  mapRoute,
  user,
  assignedOrders,
  location,
  updateAssignedOrderStatus
}) => {
  const markers = selectMarkers(assignedOrders.data);

  return {
    componentData: {
      data: markers,
      isFetching: updateAssignedOrderStatus.isFetching
    },
    user: { ...user, data: selectCachedLoginUser(user.data) },
    location: selectorLocation(location),
    markers,
    polyline: mapRoute.data.routes
      ? selectPolylineCoordinates(mapRoute.data)
      : undefined,
    orderEntityID: assignedOrders.data[0].entity_id,
    modal: true,
    orderValidity: calculateDeclineTime(assignedOrders.data)
  };
};

const actions = { mapRouteRequest, clearAssignedOrders };

export default connect(mapStateToProps, actions)(WithLoader(OrderAssignment));
