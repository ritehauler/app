// @flow
import _ from "lodash";
import React, { Component } from "react";
import { View, ScrollView } from "react-native";
import PropTypes from "prop-types";

import vehicleDataHelper from "../../../dataHelper/vehicleDataHelper";
import { Text, Separator, ImageServer } from "../../../components";
import { FeesInfo } from "../../../appComponents";
import { Strings } from "../../../theme";
import styles from "./styles";

export default class Item extends Component {
  static propTypes = {
    data: PropTypes.object.isRequired
  };

  shouldComponentUpdate(nextProps: Object) {
    return !_.isEqual(nextProps.data, this.props.data);
  }

  _renderTitle() {
    return (
      <Text type="bold" size="normalBig" textAlign="center">
        {vehicleDataHelper.getTitle(this.props.data)}
      </Text>
    );
  }

  _renderWeight() {
    return (
      <Text size="xSmall" style={styles.weight} textAlign="center">
        {vehicleDataHelper.getWeightInfo(this.props.data)}
      </Text>
    );
  }

  _renderImage() {
    return (
      <View style={styles.imageContainer}>
        <ImageServer
          style={styles.image}
          source={{
            uri: vehicleDataHelper.getImage(this.props.data)
          }}
          resizeMode="contain"
        />
      </View>
    );
  }

  _renderFees() {
    const { data } = this.props;
    return (
      <FeesInfo
        baseFee={vehicleDataHelper.getBaseFee(data)}
        perMin={vehicleDataHelper.getChargePerMin(data)}
        isOrder={false}
      />
    );
  }

  _renderCost() {
    return (
      <View style={styles.costContainer}>
        <Text style={styles.costLabel}>{Strings.estimateCost}</Text>
        <Text type="bold" size="normalBig">
          {vehicleDataHelper.getEstimatedCost(this.props.data)}
        </Text>
      </View>
    );
  }

  _renderSeparator() {
    return <Separator style={styles.separator} />;
  }

  render() {
    return (
      <ScrollView
        style={styles.container}
        contentContainerStyle={styles.contentContainerStyle}
      >
        {this._renderTitle()}
        {this._renderWeight()}
        {this._renderImage()}
        {this._renderFees()}
        {this._renderSeparator()}
        {this._renderCost()}
      </ScrollView>
    );
  }
}
