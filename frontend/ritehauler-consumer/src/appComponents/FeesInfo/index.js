// @flow
import React from "react";
import PropTypes from "prop-types";
import { View } from "react-native";

import styles from "./styles";
import { Info } from "../";
import { Strings } from "../../theme";

export default class FeesInfo extends React.PureComponent {
  static propTypes = {
    style: PropTypes.oneOfType([
      PropTypes.array,
      PropTypes.object,
      PropTypes.number
    ]),
    baseFee: PropTypes.string.isRequired,
    perMin: PropTypes.string.isRequired,
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
    const { baseFee, perMin, style } = this.props;
    return (
      <View style={[styles.container, style]}>
        {this._renderFeeItem(Strings.baseFee, baseFee)}
        {this._renderFeeItem(Strings.perMin, perMin)}
      </View>
    );
  }
}

/*
minimum: PropTypes.string.isRequired,
minimum
{this._renderFeeItem(Strings.minimum, minimum)}
*/
