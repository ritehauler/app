// @flow
import { connect } from "react-redux";
import React, { Component } from "react";
import { View, ScrollView, FlatList, SafeAreaView } from "react-native";
import { Actions } from "react-native-router-flux";
import PropTypes from "prop-types";
import moment from "moment";
import {
  Text,
  SectionText,
  Separator,
  BottomButton,
  LoadingRequest,
  Loading
} from "../../components";
import OrderItem from "./OrderItem";
import DeliveryDetail from "./DeliveryDetail";
import OrderLocationCard from "./OrderLocationCard";
import TruckDetailCard from "./TruckDetailCard";
import Utils from "../../util";
import { TripPresenter } from "../../presenter/index";
import { Metrics, Colors } from "../../theme";
import WithLoader from "../HOC/WithLoader";
import {
  ENTITY_TYPE_ID_UPDATE_ORDER_STATUS,
  USER_ENTITY_TYPE_ID,
  ENTITY_TYPE_ORDER_ITEM,
  ENTITY_TYPE_ID_MY_ORDERS,
  GMT_TIME_FORMAT,
  SUMMARY_DATE_FORMAT,
  STATUS_ACCEPTED
} from "../../constant";

// redux imports
import {
  selectCachedLoginUser,
  selectOrderItems,
  selectAssignedOrderDetail,
  selectTruckDetails,
  selectDeliveryDetails,
  splitInToExtraItems
} from "../../reducers/reduxSelectors";
import { request as orderItemsRequest } from "../../actions/OrderItemsActions";
import { request as orderDetailsRequest } from "../../actions/OrderDetailsActions";
import { request as updateAssignedOrderStatusRequest } from "../../actions/UpdateAssignedOrderStatus";

class OrderSummary extends Component {
  static propTypes = {
    isAcceptable: PropTypes.bool,
    orderEntityID: PropTypes.number
  };

  static defaultProps = {
    isAcceptable: false,
    orderEntityID: undefined
  };

  componentDidMount() {
    this.props.orderDetailsRequest({
      entity_id: this.props.orderEntityID,
      entity_type_id: ENTITY_TYPE_ID_MY_ORDERS,
      detail_key: "customer_id,truck_id,profession_id,vehicle_id",
      hook: "order_pickup,order_dropoff"
    });
    this.props.orderItemsRequest({
      order_id: this.props.orderEntityID,
      entity_type_id: ENTITY_TYPE_ORDER_ITEM
    });

    this.cbOnAcceptPress = this.cbOnAcceptPress.bind(this);
  }

  renderSectionText(value) {
    return (
      <SectionText
        title={value}
        color="sectionLabel"
        type="base"
        size="normal"
      />
    );
  }

  renderOrderLocationDetails() {
    if (!this.props.orderDetails) return null;
    const {
      image,
      name,
      order_dropoff,
      order_pickup
    } = this.props.orderDetails;

    return (
      <OrderLocationCard
        user={{
          name,
          image
        }}
        pickLocation={order_pickup}
        dropLocation={order_dropoff}
      />
    );
  }

  renderOrderItemsList() {
    return (
      <FlatList
        data={this.props.items.baseItems}
        keyExtractor={item => `${item.title}_${item.quantity}`}
        renderItem={({ item }) => (
          <OrderItem
            title={item.title}
            dimensions={item.dimensions}
            quantity={item.quantity}
            dollar={item.dollar}
          />
        )}
        ItemSeparatorComponent={() => (
          <View
            style={{
              paddingHorizontal: Metrics.baseMargin,
              backgroundColor: Colors.background.primary
            }}
          >
            <Separator />
          </View>
        )}
      />
    );
  }

  renderExtraItems() {
    if (!this.props.items.extraItems.length) return null;
    return (
      <FlatList
        ListHeaderComponent={() =>
          this.renderSectionText("Extra items added by driver")
        }
        data={this.props.items.extraItems}
        keyExtractor={item => `${item.title}_${item.quantity}`}
        renderItem={({ item }) => {
          return (
            <OrderItem
              title={item.title}
              dimensions={item.dimensions}
              quantity={item.quantity}
              dollar={item.dollar}
              isExtraItem={item.isExtraItem}
              perExtraItemCharge={item.perExtraItemCharge}
            />
          );
        }}
        ItemSeparatorComponent={() => (
          <View
            style={{
              paddingHorizontal: Metrics.baseMargin,
              backgroundColor: Colors.background.primary
            }}
          >
            <Separator />
          </View>
        )}
      />
    );
  }

  renderTruckDetails() {
    if (!this.props.truckDetails) return null;
    const {
      truck,
      baseFee,
      perMin,
      minimum,
      minEstimatedCharges,
      maxEstimatedCharges
    } = this.props.truckDetails;
    return (
      <View
        style={{
          marginTop: Metrics.baseMargin
        }}
      >
        <TruckDetailCard
          truck={truck}
          baseFee={baseFee}
          perMin={perMin}
          minimum={minimum}
          minEstimatedCharges={minEstimatedCharges}
          maxEstimatedCharges={maxEstimatedCharges}
        />
      </View>
    );
  }

  renderDeliveryDetails() {
    if (
      this.props.deliveryDetails &&
      this.props.deliveryDetails.deliveryProfessionalsCount &&
      this.props.deliveryDetails.loadingPrice
    ) {
      const {
        deliveryProfessionalsCount,
        loadingPrice
      } = this.props.deliveryDetails;
      return (
        <View style={{ marginTop: Metrics.baseMargin }}>
          <DeliveryDetail
            proffesionals={deliveryProfessionalsCount}
            loadingPrice={loadingPrice}
          />
        </View>
      );
    }

    return null;
  }

  renderBottomButton() {
    return this.props.isAcceptable ? (
      <BottomButton title={"Accept"} onPress={this.cbOnAcceptPress} />
    ) : null;
  }

  cbOnAcceptPress() {
    this.props.updateAssignedOrderStatusRequest({
      entity_type_id: ENTITY_TYPE_ID_UPDATE_ORDER_STATUS,
      order_status: STATUS_ACCEPTED,
      order_id: this.props.orderEntityID,
      driver_id: this.props.user.entity_id,
      login_entity_id: this.props.user.entity_id,
      login_entity_type_id: USER_ENTITY_TYPE_ID
    });
  }

  renderLoadingModal() {
    return this.props.updateAssignedOrderStatusFetching ? (
      <Loading loading={this.props.updateAssignedOrderStatusFetching} />
    ) : null;
  }

  render() {
    if (this.props.isFetching) return <LoadingRequest />;

    const pickupDate =
      this.props.orderDetails && this.props.orderDetails.pickupDate
        ? Utils.formatDateLocal(
            `${this.props.orderDetails.pickupDate} ${
              this.props.orderDetails.pickupTime
            }`,
            GMT_TIME_FORMAT,
            SUMMARY_DATE_FORMAT
          )
        : "";

    return (
      <SafeAreaView
        style={{ flex: 1, backgroundColor: Colors.background.login }}
      >
        <ScrollView>
          <View
            style={{
              paddingHorizontal: Metrics.baseMargin,
              paddingBottom: Metrics.baseMargin
            }}
          >
            {this.renderSectionText(pickupDate)}
            {this.renderOrderLocationDetails()}
            {this.renderSectionText("Items")}
            {this.renderOrderItemsList()}
            {this.renderExtraItems()}
            {this.renderTruckDetails()}
            {this.renderDeliveryDetails()}
            {this.renderLoadingModal()}
          </View>
          {this.renderBottomButton()}
        </ScrollView>
      </SafeAreaView>
    );
  }
}

const mapStateToProps = (
  { orderItems, user, todaysOrders, orderDetails, updateAssignedOrderStatus },
  ownProps
) => {
  const rest = orderDetails.data.length
    ? {
        orderDetails: selectAssignedOrderDetail(orderDetails.data[0]),
        truckDetails: selectTruckDetails(orderDetails.data[0]),
        deliveryDetails: selectDeliveryDetails(orderDetails.data[0])
      }
    : {
        orderDetails: undefined,
        truckDetails: undefined,
        deliveryDetails: undefined
      };

  return {
    orderEntityID: ownProps.orderEntityID
      ? ownProps.orderEntityID
      : todaysOrders.data[0].entity_id,
    isFetching: orderItems.isFetching || orderDetails.isFetching,
    user: selectCachedLoginUser(user.data),
    items: splitInToExtraItems(selectOrderItems(orderItems.data)),
    updateAssignedOrderStatusFetching: updateAssignedOrderStatus.isFetching,
    ...rest
  };
};

const actions = {
  orderItemsRequest,
  orderDetailsRequest,
  updateAssignedOrderStatusRequest
};

export default connect(mapStateToProps, actions)(OrderSummary);
