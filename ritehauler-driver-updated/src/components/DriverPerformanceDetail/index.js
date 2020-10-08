// @flow
import React, { Component } from "react";
import { View, Image } from "react-native";
import PropTypes from "prop-types";
import { Text } from "../../components";
import styles from "./styles";
import { Images, Colors, ApplicationStyles } from "../../theme";
import { PerformancePresenter } from "../../presenter";

class DriverPerformanceDetail extends Component {
  static propTypes = {
    isWeekly: PropTypes.bool,
    customContainerStyle: PropTypes.oneOfType([
      PropTypes.object,
      PropTypes.number
    ]),
    customCellStyle: PropTypes.oneOfType([PropTypes.object, PropTypes.number]),
    currencyCode: PropTypes.string,
    data: PropTypes.array
  };

  static defaultProps = {
    isWeekly: false,
    customContainerStyle: {},
    customCellStyle: {},
    currencyCode: "",
    data: []
  };

  renderLastWeekData(item) {
    const { isWeekly, currencyCode } = this.props;
    if (isWeekly) {
      const { title, last_value } = item;
      return (
        <Text style={ApplicationStyles.dLight12Grey}>Last : {last_value}</Text>
      );
    }

    return null;
  }

  renderCellView(item) {
    const { id, title, value, last_value, score } = item;
    const isPerformed = value > last_value ? true : false;
    const image = isPerformed ? Images.iconStatsUp : Images.iconStatsDown;
    const countColor = isPerformed ? Colors.accent3 : Colors.accent2;

    return (
      <View key={id} style={[styles.cellStyle, this.props.customCellStyle]}>
        <Image source={image} style={styles.imageStyle} />
        <Text color={countColor} size="large" type="medium">
          {score}
        </Text>
        <Text size="xxSmall" type={this.props.isWeekly ? "medium" : "light"}>
          {title}
        </Text>
        {this.renderLastWeekData(item)}
      </View>
    );
  }

  render() {
    return (
      <View
        style={[styles.cellContainerStyle, this.props.customContainerStyle]}
      >
        {this.props.data.map(item => this.renderCellView(item))}
      </View>
    );
  }
}

export default DriverPerformanceDetail;
