// @flow
import React, { Component } from "react";
import { Keyboard, View } from "react-native";
import { Actions } from "react-native-router-flux";
import { connect } from "react-redux";
import PropTypes from "prop-types";

import { ServerRequestPage } from "../../components";
import MapViewTrack from "./MapViewTrack";
import styles from "./styles";
import DataHandler from "../../util/DataHandler";
import { Strings } from "../../theme";

import {
  request as trackOrderRequest,
  resetTrackOrder
} from "../../actions/TrackOrderActions";
import { updateNotificationStatus } from "../../actions/NotificationListingActions";
import { trackOrderDataHelper } from "../../dataHelper";
import { TRACK_UPDATE_TIME } from "../../constants";

class TrackOrder extends Component {
  static propTypes = {
    updateNotificationStatus: PropTypes.func.isRequired,
    trackOrderRequest: PropTypes.func.isRequired,
    resetTrackOrder: PropTypes.func.isRequired,
    trackOrder: PropTypes.object.isRequired,
    networkInfo: PropTypes.object.isRequired,
    orderId: PropTypes.number.isRequired,
    historyId: PropTypes.number
  };

  static defaultProps = { historyId: -1 };

  constructor(props) {
    super(props);
    this.fetchData = this.fetchData.bind(this);
    this.fetchTrackRequest = this.fetchTrackRequest.bind(this);
    this.renderContent = this.renderContent.bind(this);
    this.fetchDataPushNotification = this.fetchDataPushNotification.bind(this);
  }

  componentWillMount() {
    Keyboard.dismiss();
  }

  componentDidMount() {
    this.fetchData();
    DataHandler.setCallBackTrackOrder(this.fetchDataPushNotification);
  }

  componentWillUnmount() {
    this.props.resetTrackOrder();
    DataHandler.setCallBackTrackOrder(undefined);
    clearInterval(this._interval);
  }

  fetchData() {
    // clear previous interval
    if (this._interval) {
      clearInterval(this._interval);
    }

    // get order and history id
    const historyId = this.historyId || this.props.historyId;
    const orderId = this.orderId || this.props.orderId;

    // send request for history id
    if (historyId !== -1) {
      this.props.updateNotificationStatus(historyId, orderId);
    }

    // get data
    this.fetchTrackRequest(true);

    // set interval to get latest data
    this._interval = setInterval(() => {
      this.fetchTrackRequest(false);
    }, TRACK_UPDATE_TIME);
  }

  fetchTrackRequest(reset) {
    const orderId = this.orderId || this.props.orderId;
    // send track order request
    const payload = {
      order_id: orderId,
      mobile_json: 1
    };
    this.props.trackOrderRequest(payload, reset);
  }

  fetchDataPushNotification = (historyId, orderId, orderNumber) => {
    this.historyId = historyId;
    this.orderId = orderId;
    Actions.refresh({
      title: orderNumber
    });
    this.fetchData();
  };

  historyId = undefined;
  orderId = undefined;

  _renderMap() {
    const { data } = this.props.trackOrder;

    return (
      <MapViewTrack
        pickUpLatLong={trackOrderDataHelper.getPickUpLatLong(data)}
        dropOffLatLong={trackOrderDataHelper.getDropOffLatLong(data)}
        coordinates={trackOrderDataHelper.getCoordinates(data)}
      />
    );
  }

  renderContent() {
    return <View style={styles.container}>{this._renderMap()}</View>;
  }

  render() {
    const { networkInfo, trackOrder } = this.props;
    const { data, isFetching, errorMessage, failure } = trackOrder;
    const isInternetConnected = networkInfo.isNetworkConnected;
    return (
      <ServerRequestPage
        data={data}
        errorMessage={errorMessage}
        failure={failure}
        renderView={this.renderContent}
        isFetching={isFetching}
        isInternetConnected={isInternetConnected}
        fetchData={this.fetchData}
        emptyMessage={Strings.noTrackingFound}
      />
    );
  }
}

const mapStateToProps = store => ({
  trackOrder: store.trackOrder,
  networkInfo: store.networkInfo
});
const actions = {
  updateNotificationStatus,
  trackOrderRequest,
  resetTrackOrder
};

export default connect(
  mapStateToProps,
  actions
)(TrackOrder);
