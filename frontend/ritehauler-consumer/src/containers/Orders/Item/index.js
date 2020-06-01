// @flow
import React from "react";
import { View, Image } from "react-native";
import PropTypes from "prop-types";
import { Actions } from "react-native-router-flux";

import { DISPLAY_DATE_TIME_FORMAT_ORDER_ITEM } from "../../../constants";
import { orderDataHelper } from "../../../dataHelper";
import { Images } from "../../../theme";
import styles from "./styles";
import { Text, ButtonView } from "../../../components";

export default class Item extends React.PureComponent {
  static propTypes = {
    data: PropTypes.object.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    this.onPressItem = this.onPressItem.bind(this);
  }

  onPressItem() {
    const { data } = this.props;
    Actions.orderDetail({
      orderId: data.entity_id,
      title: orderDataHelper.getOrderId(data)
    });
  }

  _renderDateAndStatus() {
    const { data } = this.props;
    return (
      <View style={styles.dateAndStatusContainer}>
        <Text style={styles.date} type="bold" size="small">
          {orderDataHelper.getFormatDateAndTime(
            data.pickup_date,
            data.pickup_time,
            DISPLAY_DATE_TIME_FORMAT_ORDER_ITEM
          )}
        </Text>
        <Text size="xxxSmall" color={orderDataHelper.getOrderStatusColor(data)}>
          {orderDataHelper.getOrderStatusTitle(data)}
        </Text>
        {orderDataHelper.driverCancelRequiredPayment(data) && (
          <Image source={Images.expensive} />
        )}
      </View>
    );
  }

  _renderAddress() {
    return (
      <Text size="xSmall" color="secondary" style={styles.address}>
        {orderDataHelper.getPickUpAddress(this.props.data)}
      </Text>
    );
  }

  _renderInfo() {
    const { data } = this.props;
    const cardName = orderDataHelper.getOrderCardName(data);
    return (
      <View style={styles.infoContainer}>
        <Text style={styles.info} type="semiBold" size="small">
          {orderDataHelper.getOrderId(data)} |{" "}
          {orderDataHelper.getOrderAmount(data)}
          {cardName !== "" && ` | ${cardName}`}
        </Text>
        <Image source={Images.navigation} />
      </View>
    );
  }

  render() {
    return (
      <ButtonView style={styles.container} onPress={this.onPressItem}>
        {this._renderDateAndStatus()}
        {this._renderAddress()}
        {this._renderInfo()}
      </ButtonView>
    );
  }
}
