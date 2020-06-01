// @flow
import React, { Component } from "react";
import { View, Keyboard } from "react-native";
import { connect } from "react-redux";
import PropTypes from "prop-types";

import {
  GradientButton,
  OrderItemsList,
  OrderHeader,
  OrderFooter
} from "../../appComponents";
import {
  userDataHelper,
  vehicleDataHelper,
  orderPlaceDataHelper
} from "../../dataHelper";
import { Text, ButtonView, Loader } from "../../components";
import { Strings } from "../../theme";
import styles from "./styles";
import Utils from "../../util";

import { request as orderPlaceRequest } from "../../actions/OrderPlaceActions";

class OrderSummary extends Component {
  static propTypes = {
    orderInfo: PropTypes.object.isRequired,
    orderPlace: PropTypes.object.isRequired,
    user: PropTypes.object.isRequired,
    orderPlaceRequest: PropTypes.func.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    // bind methods
    this.onPressConfirm = this.onPressConfirm.bind(this);
    this.renderCustomViewButton = this.renderCustomViewButton.bind(this);

    // set payload for request
    this.payLoadOrderPlace = orderPlaceDataHelper.getPayLoadOrderPlace(
      props.orderInfo,
      props.user
    );
  }

  componentWillMount() {
    Keyboard.dismiss();
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.orderPlace.isFetching !== this.props.orderPlace.isFetching) {
      this.loader.setLoading(nextProps.orderPlace.isFetching);
    }
  }

  shouldComponentUpdate(nextProps) {
    return false;
  }

  onPressConfirm() {
    const isCheckedConfirmBox = this.orderFooter.isChecked();
    // check if confirm box check
    if (isCheckedConfirmBox) {
      // send place order request
      this.props.orderPlaceRequest(this.payLoadOrderPlace);
    } else {
      // display error message and send to scroll to end of list to show checkbox
      Utils.alert(Strings.orderSummaryConfirmText);
      this.listItems.scrollToBottom();
      //this.listItems.getReference().scrollToEnd();
    }
  }

  _renderHeader() {
    const {
      pickup,
      dropoff,
      pickup_date,
      pickup_time
    } = this.props.orderInfo.info;
    return (
      <OrderHeader
        pickUpLocation={pickup.address}
        dropOffLocation={dropoff.address}
        userName={userDataHelper.getUserName(this.props.user)}
        userImage={{ uri: userDataHelper.getUserImage(this.props.user) }}
        date={pickup_date}
        time={pickup_time}
      />
    );
  }

  _renderFooter() {
    const { deliveryProfessional, info, vehicle } = this.props.orderInfo;
    const deliveryProfessionals =
      info.professional_id === "" ? "" : deliveryProfessional.number_of_labour;
    const loadingPrice = deliveryProfessional.price
      ? Utils.getFormattedPrice(deliveryProfessional.price)
      : "";
    return (
      <OrderFooter
        truck={vehicleDataHelper.getTitle(vehicle)}
        baseFee={vehicleDataHelper.getBaseFee(vehicle)}
        perMin={vehicleDataHelper.getChargePerMin(vehicle)}
        deliveryProfessionals={deliveryProfessionals}
        loadingPrice={loadingPrice}
        estimateCost={vehicleDataHelper.getEstimatedCost(vehicle)}
        hasConfirmCheckbox
        ref={ref => {
          this.orderFooter = ref;
        }}
      />
    );
  }

  _renderItems() {
    const { items } = this.props.orderInfo;
    return (
      <OrderItemsList
        data={items}
        ref={ref => {
          this.listItems = ref;
        }}
        ListHeaderComponent={this._renderHeader()}
        ListFooterComponent={this._renderFooter()}
        disableRipple
      />
    );
  }

  _renderButton() {
    return (
      <GradientButton
        text={Strings.continueButton}
        ref={ref => {
          this.gradientButton = ref;
        }}
        disableRipple
        customView={this.renderCustomViewButton}
        setKeyboardEvent={false}
      />
    );
  }

  _renderLoading() {
    return (
      <Loader
        ref={ref => {
          this.loader = ref;
        }}
      />
    );
  }

  renderCustomViewButton() {
    return (
      <View style={styles.buttonView}>
        <Text
          style={styles.estimateCost}
          type="bold2"
          size="medium"
          color="tertiary"
          textAlign="center"
        >
          {Strings.estimateCost}{" "}
          {orderPlaceDataHelper.getTotalCost(this.props.orderInfo)}
        </Text>
        <View style={styles.midLine} />
        <ButtonView style={styles.confirm} onPress={this.onPressConfirm}>
          <Text type="bold2" size="medium" color="tertiary" textAlign="center">
            {Strings.confirm}
          </Text>
        </ButtonView>
      </View>
    );
  }

  render() {
    return (
      <View style={styles.container}>
        {this._renderItems()}
        {this._renderButton()}
        {this._renderLoading()}
      </View>
    );
  }
}

const mapStateToProps = store => ({
  orderInfo: store.orderInfo,
  orderPlace: store.orderPlace,
  user: store.user.data
});
const actions = { orderPlaceRequest };

export default connect(
  mapStateToProps,
  actions
)(OrderSummary);
