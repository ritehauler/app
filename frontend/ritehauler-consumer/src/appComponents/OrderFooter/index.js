// @flow
import _ from "lodash";
import React from "react";
import { View } from "react-native";
import PropTypes from "prop-types";

import styles from "./styles";
import {
  FeesInfo,
  DeliveryProfessionalInfo,
  Checkbox,
  OrderExtraItems
} from "../";
import { Text, Separator } from "../../components";
import { Strings } from "../../theme";
import Util from "../../util";

export default class OrderFooter extends React.PureComponent {
  static propTypes = {
    truck: PropTypes.string.isRequired,
    baseFee: PropTypes.string.isRequired,
    perMin: PropTypes.string.isRequired,
    deliveryProfessionals: PropTypes.any.isRequired,
    loadingPrice: PropTypes.string.isRequired,
    estimateCost: PropTypes.string,
    hasConfirmCheckbox: PropTypes.bool,
    extraItems: PropTypes.array
  };

  static defaultProps = {
    estimateCost: "",
    hasConfirmCheckbox: false,
    extraItems: []
  };

  isChecked = () => this.checkBox.isChecked();

  _renderSeparator() {
    return <Separator />;
  }

  _renderTruckInfo() {
    const { truck } = this.props;
    return (
      <View style={styles.truckContainer}>
        <Text style={styles.truckTitle} type="bold">
          {Strings.truck}
        </Text>
        <Text type="light" size="xSmall">
          {truck}
        </Text>
      </View>
    );
  }

  _renderFeesInfo() {
    const { baseFee, perMin } = this.props;
    return (
      <FeesInfo baseFee={baseFee} perMin={perMin} style={styles.feesInfo} />
    );
  }

  _renderEstimateCost() {
    const { estimateCost } = this.props;
    if (estimateCost === "") {
      return null;
    }

    return (
      <View>
        {this._renderSeparator()}
        <Text style={styles.estimateCost} color="accent">
          {Strings.estimateCost} {estimateCost}
        </Text>
      </View>
    );
  }

  _renderConfirmText() {
    return (
      <Text style={styles.confirmText} size="xSmall" type="light">
        {Strings.confirmAccept}{" "}
        <Text
          size="small"
          type="bold"
          onPress={() => Util.goToTermsOfServices()}
        >
          {Strings.termsOfUse}
        </Text>{" "}
        {Strings.and}{" "}
        <Text size="small" type="bold" onPress={() => Util.goToPrivacyPolicy()}>
          {Strings.privacyPolicy}
        </Text>
      </Text>
    );
  }

  _renderCheckBox() {
    return (
      <Checkbox
        ref={ref => {
          this.checkBox = ref;
        }}
      />
    );
  }

  _renderConfirmBox() {
    return (
      <View style={styles.confirmContainer}>
        {this._renderCheckBox()}
        {this._renderConfirmText()}
      </View>
    );
  }

  _renderDeliveryProfessionals() {
    const { deliveryProfessionals, loadingPrice } = this.props;
    if (deliveryProfessionals === "") {
      return null;
    }
    return (
      <DeliveryProfessionalInfo
        deliveryProfessionals={deliveryProfessionals}
        loadingPrice={loadingPrice}
        style={styles.deliveryProfessionals}
      />
    );
  }

  _renderTruckAndFeesInfo() {
    return (
      <View style={styles.truckAndFeesInfo}>
        {this._renderTruckInfo()}
        {this._renderSeparator()}
        {this._renderFeesInfo()}
        {this._renderEstimateCost()}
      </View>
    );
  }

  _renderExtraItems() {
    const { extraItems } = this.props;
    if (_.isEmpty(extraItems, true)) {
      return null;
    }
    return <OrderExtraItems data={extraItems} />;
  }

  render() {
    const { hasConfirmCheckbox } = this.props;
    return (
      <View style={styles.container}>
        {this._renderExtraItems()}
        {this._renderTruckAndFeesInfo()}
        {this._renderDeliveryProfessionals()}
        {hasConfirmCheckbox && this._renderConfirmBox()}
      </View>
    );
  }
}
