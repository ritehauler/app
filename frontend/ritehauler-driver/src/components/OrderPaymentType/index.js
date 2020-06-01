//@flow
import React, { Component } from "react";
import { View, Image } from "react-native";
import PropTypes from "prop-types";
import { Text } from "../../components";
import styles from "./styles";
import { Images } from "../../theme";

class OrderPaymentType extends Component {
  static propTypes = {
    orderId: PropTypes.string,
    amount: PropTypes.string,
    paymentType: PropTypes.string,
    selected: PropTypes.bool,
    isDetail: PropTypes.bool,
    isSelection: PropTypes.bool,
    isDepotOrderDetail: PropTypes.bool
  };

  static defaultProps = {
    orderId: "0",
    amount: "$200",
    paymentType: "COD",
    selected: false,
    isDetail: false,
    isSelection: false,
    isDepotOrderDetail: false
  };

  renderNavigationArrow(image: any) {
    return (
      <View style={styles.navigationImageContainerStyle}>
        <Image source={image} />
      </View>
    );
  }

  renderSelectionImage() {
    const image = this.props.selected
      ? Images.iconItemSelected
      : Images.iconItemUnselected;

    return (
      <View style={styles.navigationImageContainerStyle}>
        <Image source={image} />
      </View>
    );
  }

  renderRightView() {
    const { isDetail, isSelection, isDepotOrderDetail } = this.props;

    if (isDetail) {
      return this.renderNavigationArrow(Images.orderNavigation);
    } else if (isSelection) {
      return this.renderSelectionImage();
    } else if (isDepotOrderDetail) {
      return this.renderNavigationArrow(Images.orderBundleNavigation);
    }

    return null;
  }

  renderPaymentData(title: string, value: any) {
    return (
      <View style={styles.orderItemContainerStyle}>
        <Text color="orderDepot" size="xxxSmall" type="medium">
          {title}
        </Text>
        <Text type="medium">{value}</Text>
      </View>
    );
  }

  render() {
    const { orderId, amount, paymentType } = this.props;
    const { orderDetailContainerStyle } = styles;

    return (
      <View style={orderDetailContainerStyle}>
        {this.renderPaymentData("Order ID", "T" + orderId)}
        {this.renderPaymentData("Amount", amount)}
        {this.renderPaymentData("Type", paymentType)}
        {this.renderRightView()}
      </View>
    );
  }
}

export default OrderPaymentType;
