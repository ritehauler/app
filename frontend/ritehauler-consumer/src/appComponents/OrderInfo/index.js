// @flow
import React from "react";
import { View } from "react-native";
import PropTypes from "prop-types";
import { Actions } from "react-native-router-flux";

import { Strings, Images, Metrics } from "../../theme";
import styles from "./styles";
import { Text, Separator, ButtonView, ImageLoad } from "../../components";
import { orderDataHelper } from "../../dataHelper";
import Util from "../../util";

export default class OrderInfo extends React.PureComponent {
  static propTypes = {
    data: PropTypes.object.isRequired
  };

  static defaultProps = {};

  onPressDriverProfile = (driverId, driverName) => {
    Actions.driverProfile({ driverId, driverName });
  };

  _renderItem(title, value, style = {}) {
    return (
      <View style={style}>
        <Text type="semiBold" size="small">
          {title}
        </Text>
        <Text style={styles.value} size="xSmall" color="secondary">
          {value}
        </Text>
      </View>
    );
  }

  _renderDurationAndDistance() {
    const { data } = this.props;
    return (
      <View style={styles.row}>
        {this._renderItem(
          Strings.duration,
          orderDataHelper.getOrderDuration(data),
          styles.left
        )}
        {this._renderItem(
          Strings.distance,
          orderDataHelper.getOrderDistance(data),
          styles.right
        )}
      </View>
    );
  }

  _renderSeparator() {
    return <Separator style={styles.separator} />;
  }

  _renderDriver(driverId) {
    const { data } = this.props;
    const driverName = orderDataHelper.getDriverName(data);
    return (
      <ButtonView
        style={styles.driverView}
        onPress={() => this.onPressDriverProfile(driverId, driverName)}
      >
        <ImageLoad
          isShowActivity={false}
          borderRadius={Metrics.baseMargin}
          style={styles.image}
          customImagePlaceholderDefaultStyle={
            styles.customImagePlaceholderDefaultStyle
          }
          placeholderSource={Images.userPlaceholder}
          source={{
            uri: Util.getImageFromGallery(data.driver_id.detail.gallery, 0)
          }}
        />
        {this._renderItem(Strings.driver, driverName)}
      </ButtonView>
    );
  }

  _renderVehicleInfo() {
    const { data } = this.props;
    const vehiclePlate = orderDataHelper.getOrderVehicleCode(data);
    return (
      <View style={styles.row}>
        {this._renderItem(
          Strings.vehicle,
          orderDataHelper.getOrderVehicleName(data),
          vehiclePlate !== "" ? styles.left : styles.full
        )}
        {vehiclePlate !== "" &&
          this._renderItem(Strings.plate, vehiclePlate, styles.right)}
      </View>
    );
  }

  render() {
    const driverId = orderDataHelper.getDriverId(this.props.data);
    const showDriverInfo = driverId !== 0;
    return (
      <View style={styles.container}>
        {this._renderDurationAndDistance()}
        {showDriverInfo && this._renderSeparator()}
        {showDriverInfo && this._renderDriver(driverId)}
        {this._renderSeparator()}
        {this._renderVehicleInfo()}
      </View>
    );
  }
}
