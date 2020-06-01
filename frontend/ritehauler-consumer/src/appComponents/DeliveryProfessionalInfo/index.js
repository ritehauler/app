// @flow
import React from "react";
import PropTypes from "prop-types";
import { View } from "react-native";

import styles from "./styles";
import { Info } from "../";
import { Strings } from "../../theme";

export default class DeliveryProfessionalInfo extends React.PureComponent {
  static propTypes = {
    style: PropTypes.oneOfType([
      PropTypes.array,
      PropTypes.object,
      PropTypes.number
    ]),
    deliveryProfessionals: PropTypes.any.isRequired,
    loadingPrice: PropTypes.string.isRequired,
    isOrder: PropTypes.bool
  };

  static defaultProps = {
    style: {},
    isOrder: true
  };

  _renderFeeItem(title, value) {
    return <Info title={title} value={value} isOrder={this.props.isOrder} />;
  }

  render() {
    const { deliveryProfessionals, loadingPrice, style } = this.props;
    return (
      <View style={[styles.container, style]}>
        {this._renderFeeItem(
          Strings.deliveryProfessional,
          deliveryProfessionals
        )}
        {this._renderFeeItem(Strings.loadingPrice, loadingPrice)}
      </View>
    );
  }
}
