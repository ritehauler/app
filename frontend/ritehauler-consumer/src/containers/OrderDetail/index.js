// @flow
import _ from "lodash";
import React, { Component } from "react";
import { View, Keyboard, Image } from "react-native";
import { Actions } from "react-native-router-flux";
import { connect } from "react-redux";
import PropTypes from "prop-types";

import {
  GradientButton,
  OrderItemsList,
  OrderHeader,
  OrderFooter
} from "../../appComponents";
import { Text, ServerRequestPage } from "../../components";
import { orderDataHelper, userDataHelper } from "../../dataHelper";
import DataHandler from "../../util/DataHandler";
import { Images, Strings } from "../../theme";
import styles from "./styles";

import {
  ENTITY_TYPE_ID_ORDER,
  ORDER_STATUS_PAYMENT_REQUIRED
} from "../../config/WebService";
import {
  request as orderDetailRequest,
  resetOrderDetail
} from "../../actions/OrderDetailActions";
import { updateNotificationStatus } from "../../actions/NotificationListingActions";

class OrderDetail extends Component {
  static propTypes = {
    orderDetailRequest: PropTypes.func.isRequired,
    resetOrderDetail: PropTypes.func.isRequired,
    updateNotificationStatus: PropTypes.func.isRequired,
    orderDetail: PropTypes.object.isRequired,
    networkInfo: PropTypes.object.isRequired,
    orderId: PropTypes.number.isRequired,
    user: PropTypes.object.isRequired,
    historyId: PropTypes.number,
    notificationInfo: PropTypes.object.isRequired
  };

  static defaultProps = { historyId: -1 };

  constructor(props) {
    super(props);
    this.onPressButton = this.onPressButton.bind(this);
    this.renderContent = this.renderContent.bind(this);
    this.fetchData = this.fetchData.bind(this);
    this.renderCustomViewButton = this.renderCustomViewButton.bind(this);
  }

  componentWillMount() {
    Keyboard.dismiss();
  }

  componentDidMount() {
    this.fetchData();
    DataHandler.setCallBackOrderDetail(this.fetchDataPushNotification);
  }

  componentWillReceiveProps(nextProps) {
    if (
      !_.isEqual(nextProps.notificationInfo, this.props.notificationInfo) &&
      _.isEqual(nextProps.notificationInfo.orderId, this.props.orderId)
    ) {
      this.fetchData();
    }
  }

  componentWillUnmount() {
    //this.props.resetOrderDetail();
    DataHandler.setCallBackOrderDetail(undefined);
  }

  onPressButton() {
    const { data } = this.props.orderDetail;
    const selectedId = data && data.card_id ? data.card_id : "";
    const orderNumber = data && data.order_number ? data.order_number : "";
    Actions.paymentMethod({
      isOrderDetail: true,
      orderId: this.props.orderId,
      order_number: orderNumber,
      orderStatus: orderDataHelper.getOrderStatus(data),
      selectedId
    });
  }

  fetchDataPushNotification = (historyId, orderId, orderNumber) => {
    this.historyId = historyId;
    this.orderId = orderId;
    Actions.refresh({
      title: orderNumber
    });
    this.fetchData();
  };

  fetchData() {
    const historyId = this.historyId || this.props.historyId;
    const orderId = this.orderId || this.props.orderId;
    const payload = {
      entity_type_id: ENTITY_TYPE_ID_ORDER,
      entity_id: orderId,
      hook: "order_item,order_pickup,order_dropoff",
      detail_key: "order_status,vehicle_id,driver_id",
      order_pickup_param: "address",
      order_dropoff_param: "address",
      mobile_json: 1
    };
    this.props.orderDetailRequest(payload);
    if (historyId !== -1) {
      this.props.updateNotificationStatus(historyId, orderId);
    }
  }

  historyId = undefined;
  orderId = undefined;

  _renderHeader() {
    const { data } = this.props.orderDetail;
    return (
      <OrderHeader
        pickUpLocation={orderDataHelper.getPickUpAddress(data)}
        dropOffLocation={orderDataHelper.getDropOffAddress(data)}
        userName={userDataHelper.getUserName(this.props.user)}
        userImage={{ uri: userDataHelper.getUserImage(this.props.user) }}
        date={data.pickup_date}
        time={data.pickup_time}
        orderInfo={data}
        orderStatusColor={orderDataHelper.getOrderStatusColor(data)}
        orderStatusTitle={orderDataHelper.getOrderStatusTitle(data)}
        orderStatus={orderDataHelper.getOrderStatus(data)}
        driverCancelPaymentRequired={orderDataHelper.driverCancelRequiredPayment(
          data
        )}
      />
    );
  }

  _renderFooter(extraItems) {
    const { data } = this.props.orderDetail;
    return (
      <OrderFooter
        truck={orderDataHelper.getOrderVehicleName(data)}
        baseFee={orderDataHelper.getBaseFee(data)}
        perMin={orderDataHelper.getPerMinutePrice(data)}
        deliveryProfessionals={orderDataHelper.getDeliveryProfessional(data)}
        loadingPrice={orderDataHelper.getLoadingPrice(data)}
        estimateCost={orderDataHelper.getEstimatedCost(data)}
        extraItems={extraItems}
        ref={ref => {
          this.orderFooter = ref;
        }}
      />
    );
  }

  _renderItems() {
    const { data } = this.props.orderDetail;
    const items = orderDataHelper.getOrderItems(data);
    return (
      <OrderItemsList
        data={items.itemsList}
        ref={ref => {
          this.listItems = ref;
        }}
        ListHeaderComponent={this._renderHeader()}
        ListFooterComponent={this._renderFooter(items.extraItemList)}
        disableRipple
      />
    );
  }

  _renderButton() {
    const { data } = this.props.orderDetail;
    const orderStatus = orderDataHelper.getOrderStatus(data);
    const driverCancelPaymentRequired = orderDataHelper.driverCancelRequiredPayment(
      data
    );

    const enableButton =
      orderStatus === ORDER_STATUS_PAYMENT_REQUIRED ||
      driverCancelPaymentRequired;
    const rest = enableButton
      ? {
          customView: this.renderCustomViewButton,
          onPress: this.onPressButton
        }
      : { isDisable: true, text: orderDataHelper.getOrderAmount(data) };

    return (
      <GradientButton
        ref={ref => {
          this.gradientButton = ref;
        }}
        setKeyboardEvent={false}
        {...rest}
      />
    );
  }

  renderCustomViewButton() {
    const { data } = this.props.orderDetail;
    return (
      <View style={styles.customButtonView}>
        <Text
          style={styles.amountButton}
          type="bold2"
          size="medium"
          color="tertiary"
          textAlign="center"
        >
          {orderDataHelper.getOrderAmount(data)}
        </Text>
        <Image source={Images.nextOrder} style={styles.imageArrowButton} />
      </View>
    );
  }

  renderContent() {
    return (
      <View style={styles.container}>
        {this._renderItems()}
        {this._renderButton()}
      </View>
    );
  }

  render() {
    const { networkInfo, orderDetail } = this.props;
    const { data, isFetching, errorMessage, failure } = orderDetail;
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
        emptyMessage={Strings.noOrderDetailFound}
      />
    );
  }
}

const mapStateToProps = store => ({
  orderDetail: store.orderDetail,
  networkInfo: store.networkInfo,
  user: store.user.data,
  notificationInfo: store.notificationInfo
});
const actions = {
  orderDetailRequest,
  resetOrderDetail,
  updateNotificationStatus
};

export default connect(
  mapStateToProps,
  actions
)(OrderDetail);
