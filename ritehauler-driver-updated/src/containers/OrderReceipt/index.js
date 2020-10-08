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
  LoadingRequest
} from "../../components";
import OrderItem from "../OrderSummary/OrderItem";
import DeliveryDetail from "../OrderSummary/DeliveryDetail";
import TruckDetailCard from "../OrderSummary/TruckDetailCard";
import OrderLocationCard from "../OrderSummary/OrderLocationCard";
import VehicleDetail from "./VehicleDetail";
import Utils from "../../util";
import { Metrics, Colors, Images } from "../../theme";
import {
  ENTITY_TYPE_ID_MY_ORDERS,
  ENTITY_TYPE_ORDER_ITEM,
  SUMMARY_DATE_FORMAT,
  GMT_TIME_FORMAT
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

class OrderReceipt extends Component {
  componentDidMount() {
    this.props.orderDetailsRequest({
      entity_id: this.props.orderEntityId,
      entity_type_id: ENTITY_TYPE_ID_MY_ORDERS,
      detail_key: "customer_id,truck_id,profession_id,vehicle_id",
      hook: "order_pickup,order_dropoff"
    });

    this.props.orderItemsRequest({
      order_id: this.props.orderEntityId,
      entity_type_id: ENTITY_TYPE_ORDER_ITEM
    });
  }

  renderSectionText(title) {
    return (
      <SectionText
        title={title}
        color="sectionLabel"
        type="base"
        size="normal"
      />
    );
  }

  renderOrderLocationCard() {
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

  renderVehicleDetail() {
    if (!this.props.truckDetails) return null;
    const {
      driverName,
      truck,
      baseFee,
      perMin,
      minimum,
      noPlate,
      minEstimatedCharges,
      maxEstimatedCharges,
      totalDistance,
      totalMinutes
    } = this.props.truckDetails;
    const { thumb } = this.props.user;

    return (
      <View style={{ marginTop: Metrics.baseMargin }}>
        <VehicleDetail
          driverName={driverName}
          vehicle={truck}
          noPlate={noPlate}
          duration={totalMinutes}
          distance={totalDistance}
          driverImage={thumb}
        />
      </View>
    );
  }

  renderOrderItems() {
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
        renderItem={({ item }) => (
          <OrderItem
            title={item.title}
            dimensions={item.dimensions}
            quantity={item.quantity}
            dollar={item.dollar}
            isExtraItem={item.isExtraItem}
            perExtraItemCharge={item.perExtraItemCharge}
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

  renderTruckDetail() {
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

  renderDeliveryDetail() {
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

  renderButton() {
    if (!this.props.orderDetails || !this.props.orderDetails.grand_total)
      return (
        <View
          style={{
            height: Metrics.baseMargin,
            backgroundColor: Colors.background.login
          }}
        />
      );
    return (
      <View
        style={{
          marginTop: Metrics.baseMargin
        }}
      >
        <BottomButton
          title={`$${this.props.orderDetails.grand_total}`}
          greyBackground={this.props.hideReceipt ? true : false}
          image={Images.forward}
          onPress={() => {
            this.props.hideReceipt
              ? {}
              : Actions.rateAgent({
                  orderID: this.props.orderEntityId
                });
          }}
        />
      </View>
    );
  }

  render() {
    if (this.props.isFetching) return <LoadingRequest />;
    return (
      <SafeAreaView
        style={{ flex: 1, backgroundColor: Colors.background.login }}
      >
        <ScrollView>
          <View style={{ paddingHorizontal: Metrics.baseMargin }}>
            {this.renderSectionText(
              this.props.orderDetails && this.props.orderDetails.pickupDate
                ? Utils.formatDateLocal(
                    `${this.props.orderDetails.pickupDate} ${
                      this.props.orderDetails.pickupTime
                    }`,
                    GMT_TIME_FORMAT,
                    SUMMARY_DATE_FORMAT
                  )
                : ""
            )}
            {this.renderOrderLocationCard()}
            {this.renderVehicleDetail()}
            {this.renderSectionText("Items")}
            {this.renderOrderItems()}
            {this.renderExtraItems()}
            {this.renderTruckDetail()}
            {this.renderDeliveryDetail()}
          </View>
          {this.renderButton()}
        </ScrollView>
      </SafeAreaView>
    );
  }
}

const mapStateToProps = (
  { orderItems, user, todaysOrders, orderDetails, locationInfo },
  ownProps
) => {
  return {
    orderDetails: orderDetails.data.length
      ? selectAssignedOrderDetail(orderDetails.data[0])
      : undefined,
    isFetching: orderItems.isFetching || orderDetails.isFetching,
    user: selectCachedLoginUser(user.data),
    items: splitInToExtraItems(selectOrderItems(orderItems.data)),
    orderEntityId: ownProps.orderEntityId
      ? ownProps.orderEntityId
      : todaysOrders.data[0].entity_id,
    truckDetails: orderDetails.data.length
      ? selectTruckDetails(orderDetails.data[0])
      : undefined,
    deliveryDetails: orderDetails.data.length
      ? selectDeliveryDetails(orderDetails.data[0])
      : undefined,
    orderItems,
    locationInfo
  };
};

const actions = { orderDetailsRequest, orderItemsRequest };

export default connect(mapStateToProps, actions)(OrderReceipt);
