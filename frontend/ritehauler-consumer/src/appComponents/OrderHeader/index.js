// @flow
import _ from "lodash";
import React from "react";
import PropTypes from "prop-types";
import { Actions } from "react-native-router-flux";
import { View, Image } from "react-native";

import { ORDER_STATUS_ON_THE_WAY } from "../../config/WebService";
import { PickUpLocation, DropOffLocation, User, OrderInfo } from "../";
import { orderDataHelper } from "../../dataHelper";
import { Text, ButtonView } from "../../components";
import { Strings, Images } from "../../theme";
import styles from "./styles";

export default class OrderHeader extends React.PureComponent {
  static propTypes = {
    pickUpLocation: PropTypes.string.isRequired,
    dropOffLocation: PropTypes.string.isRequired,
    userName: PropTypes.string.isRequired,
    userImage: PropTypes.oneOfType([PropTypes.number, PropTypes.object])
      .isRequired,
    date: PropTypes.string.isRequired,
    time: PropTypes.string.isRequired,
    orderInfo: PropTypes.object,
    onPressUser: PropTypes.func,
    orderStatusTitle: PropTypes.string,
    orderStatusColor: PropTypes.string,
    orderStatus: PropTypes.string,
    driverCancelPaymentRequired: PropTypes.bool
  };

  static defaultProps = {
    orderInfo: {},
    onPressUser: () => {},
    orderStatusTitle: "",
    orderStatusColor: "",
    orderStatus: "",
    driverCancelPaymentRequired: false
  };

  _renderPickup() {
    const { pickUpLocation } = this.props;
    return (
      <PickUpLocation
        text={pickUpLocation}
        disableRipple
        showLine
        ref={ref => {
          this.pickUpLocation = ref;
        }}
      />
    );
  }

  _renderDropOff() {
    const { dropOffLocation } = this.props;
    return (
      <DropOffLocation
        text={dropOffLocation}
        disableRipple
        ref={ref => {
          this.dropOffLocation = ref;
        }}
      />
    );
  }

  _renderOrderStatusTitle(orderStatusColor, orderStatusTitle) {
    return (
      <Text size="xxxSmall" color={orderStatusColor} style={styles.orderStatus}>
        {orderStatusTitle}
      </Text>
    );
  }

  _renderTrackButton() {
    const { entity_id, order_number } = this.props.orderInfo;
    return (
      <ButtonView
        style={styles.trackButton}
        onPress={() =>
          Actions.trackOrder({
            orderId: entity_id,
            title: `${order_number}`
          })
        }
      >
        <Text color="accent" size="xxSmall">
          {Strings.track}
        </Text>
      </ButtonView>
    );
  }

  _renderUser() {
    const {
      userName,
      userImage,
      onPressUser,
      orderStatusTitle,
      orderStatus,
      orderStatusColor,
      driverCancelPaymentRequired
    } = this.props;
    return (
      <View style={styles.userContainer}>
        <User name={userName} image={userImage} onPress={onPressUser} />
        {orderStatusTitle !== "" &&
          this._renderOrderStatusTitle(orderStatusColor, orderStatusTitle)}
        {orderStatus === ORDER_STATUS_ON_THE_WAY && this._renderTrackButton()}
        {driverCancelPaymentRequired && (
          <Image source={Images.expensive} style={styles.cancelPaymentIcon} />
        )}
      </View>
    );
  }

  _renderDate() {
    const { date, time } = this.props;
    /*
    const formatDate = Utils.formatDate(
      date,
      DATE_FORMAT,
      DISPLAY_DATE_FORMAT_ORDER
    );
    const formatTime = Utils.formatDate(time, TIME_FORMAT, DISPLAY_TIME_FORMAT);
    {formatDate}, {formatTime}
      */
    return (
      <Text style={styles.date} size="xxSmall">
        {orderDataHelper.getFormatDateAndTime(date, time)}
      </Text>
    );
  }

  _renderItemsTitle() {
    return (
      <Text size="xxSmall" style={styles.items}>
        {Strings.items}
      </Text>
    );
  }

  _renderContent() {
    return (
      <View style={styles.container}>
        {this._renderUser()}
        {this._renderPickup()}
        {this._renderDropOff()}
      </View>
    );
  }

  _renderOrderInfo() {
    const { orderInfo } = this.props;
    if (_.isEmpty(orderInfo, true)) {
      return null;
    }
    return <OrderInfo data={orderInfo} />;
  }

  render() {
    return (
      <View>
        {this._renderDate()}
        {this._renderContent()}
        {this._renderOrderInfo()}
        {this._renderItemsTitle()}
      </View>
    );
  }
}
