import React, { PureComponent } from "react";
import { connect } from "react-redux";
import { Actions } from "react-native-router-flux";
import { Alert } from "react-native";
import DetailCard from "./DetailCard";
import {
  ENTITY_TYPE_ID_UPDATE_ORDER_STATUS,
  USER_ENTITY_TYPE_ID,
  DRIVER_ACCEPTED,
  DRIVER_ARRIVED,
  DRIVER_ASSIGNED,
  ON_THE_WAY,
  ABOUT_TO_REACH,
  RIDE_MODE,
  DRIVER_REACHED
} from "../../../../constant";
import Utils from "../../../../util";
import {
  GOOGLE_API_KEY,
  API_GOOGLE_DIRECTION
} from "../../../../config/WebService";
import util from "../../../../util";

// redux imports
import {
  selectAssignedOrderDetail,
  selectorLocation,
  selectCachedLoginUser
} from "../../../../reducers/reduxSelectors";
import { request as updateAssignedOrderStatusRequest } from "../../../../actions/UpdateAssignedOrderStatus";
import { locallyUpdateOrderStatus } from "../../../../actions/TodaysOrdersActions";
import { hideCard } from "../../../../actions/DetailCardActions";

class DetailCardHandler extends PureComponent {
  constructor(props) {
    super(props);
    this.handleCardButtonPress = this.handleCardButtonPress.bind(this);
    this.cardActionAlert = this.cardActionAlert.bind(this);
  }

  cardActionAlert() {
    Alert.alert(
      "Confirm",
      "Are you sure?",
      [
        {
          text: "Cancel",
          onPress: () => {},
          style: "cancel"
        },
        { text: "OK", onPress: () => this.handleCardButtonPress() }
      ],
      { cancelable: true }
    );
  }

  handleCardButtonPress() {
    const { order_pickup, order_dropoff } = this.props.orderDetails;

    const status = this.props.orderDetails.status;
    if (status === DRIVER_ACCEPTED) {
      this.props.updateAssignedOrderStatusRequest({
        entity_type_id: ENTITY_TYPE_ID_UPDATE_ORDER_STATUS,
        order_status: "arrived",
        order_id: this.props.orderID,
        driver_id: this.props.driverID,
        login_entity_id: this.props.driverID,
        login_entity_type_id: USER_ENTITY_TYPE_ID
      });
    }
    if (status === DRIVER_ARRIVED) {
      Actions.verifyItems();
    }

    if (status === ON_THE_WAY) {
      this.props.locallyUpdateOrderStatus();
    }

    if (status === ABOUT_TO_REACH) {
      this.props.updateAssignedOrderStatusRequest({
        entity_type_id: ENTITY_TYPE_ID_UPDATE_ORDER_STATUS,
        order_status: "reached",
        order_id: this.props.orderID,
        driver_id: this.props.driverID,
        login_entity_id: this.props.driverID,
        login_entity_type_id: USER_ENTITY_TYPE_ID,
        total_distance: 5.2
      });
    }

    if (status === DRIVER_REACHED) {
      Actions.orderReceipt();
      this.props.hideCard();
    }
  }

  _handleCardState(status) {
    if (status === DRIVER_ACCEPTED || status === DRIVER_ARRIVED) {
      return { title: "Arrived", status: 1 };
    } else if (status === ON_THE_WAY) {
      return { title: "On the way", status: 2 };
    } else if (status === ABOUT_TO_REACH) {
      return { title: "Reached Destination", status: 3 };
    } else if (status === DRIVER_REACHED) {
      return { title: "Reached Destination", status: 4 };
    }

    return { title: "Unknown state", status: 1 };
  }

  render() {
    if (!this.props.detailCard.showCard) return null;

    if (!this.props.orderDetails) return null;

    const {
      name,
      image,
      order_dropoff,
      order_pickup,
      status,
      order_number
    } = this.props.orderDetails;

    const state = this._handleCardState(status);

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
        status={state.status}
        buttonTitle={state.title}
        cbOnButtonPress={this.cardActionAlert}
        //passing selected assigned order index to fetch its data on next screen
        index={0}
        isFetching={this.props.updateAssignedOrderStatus.isFetching}
        orderNumber={order_number}
      />
    );
  }
}

const mapStateToProps = ({
  user,
  todaysOrders,
  location,
  detailCard,
  updateAssignedOrderStatus
}) => {
  return {
    orderDetails: todaysOrders.data.length
      ? selectAssignedOrderDetail(todaysOrders.data[0])
      : undefined,
    location: selectorLocation(location),
    orderID: todaysOrders.data.length
      ? todaysOrders.data[0].entity_id
      : undefined,
    driverID: selectCachedLoginUser(user.data).entity_id,
    detailCard,
    updateAssignedOrderStatus
  };
};

const actions = {
  updateAssignedOrderStatusRequest,
  locallyUpdateOrderStatus,
  hideCard
};

export default connect(mapStateToProps, actions)(DetailCardHandler);
