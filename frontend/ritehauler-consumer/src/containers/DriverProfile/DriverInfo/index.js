// @flow
import React from "react";
import { View } from "react-native";
import LinearGradient from "react-native-linear-gradient";
import PropTypes from "prop-types";

import styles from "./styles";
import { Text, ImageLoad } from "../../../components";
import { Colors, Strings, Metrics, Images } from "../../../theme";
import { GRADIENT_START, GRADIENT_END } from "../../../constants";
import Util from "../../../util";

export default class DriverInfo extends React.PureComponent {
  static propTypes = {
    data: PropTypes.object.isRequired,
    isDetail: PropTypes.bool
  };

  static defaultProps = { isDetail: false };

  _renderItem(title, value) {
    return (
      <View style={styles.itemContainer}>
        <Text color="tertiary" type="bold" size="large" style={styles.title}>
          {title}
        </Text>
        <Text color="tertiary" type="light">
          {value}
        </Text>
      </View>
    );
  }

  _renderImage() {
    const { gallery } = this.props.data;
    return (
      <ImageLoad
        isShowActivity={false}
        borderRadius={Metrics.driverImage / 2}
        style={styles.image}
        customImagePlaceholderDefaultStyle={
          styles.customImagePlaceholderDefaultStyle
        }
        placeholderSource={Images.userPlaceholder}
        source={{ uri: Util.getImageFromGalleryObject(gallery, 1) }}
      />
    );

    /*
    <Image style={styles.image} source={Images.driver} />
    */
  }

  _renderName() {
    const { full_name } = this.props.data;
    return (
      <Text
        textAlign="center"
        style={styles.name}
        type="bold"
        size="large"
        color="tertiary"
      >
        {full_name}
      </Text>
    );
  }

  _renderTruckInfo() {
    const { truckInfo } = this.props.data;
    return (
      <Text textAlign="center" color="tertiary" type="light">
        {truckInfo}
      </Text>
    );
  }

  _renderStatistics() {
    const { trips, rating, joining_key, joining_value } = this.props.data;
    return (
      <View style={styles.statisticsContainer}>
        {this._renderItem(trips || "0", Strings.trips)}
        {this._renderItem(rating || "0", Strings.rating)}
        {this._renderItem(joining_value || "0", joining_key || Strings.years)}
      </View>
    );
  }

  render() {
    const { isDetail } = this.props;
    return (
      <LinearGradient
        colors={Colors.background.gradient}
        start={GRADIENT_START}
        end={GRADIENT_END}
      >
        {this._renderImage()}
        {this._renderName()}
        {isDetail && this._renderStatistics()}
      </LinearGradient>
    );
  }
}

/*
{isDetail && this._renderTruckInfo()}
*/
