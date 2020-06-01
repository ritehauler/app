// @flow
import _ from "lodash";
import { connect } from "react-redux";
import React, { Component } from "react";
import { View, Image, TouchableWithoutFeedback } from "react-native";
import PropTypes from "prop-types";
import { Text, ButtonView, ImageView, CircleTextView } from "../../components";
import styles from "./styles";
import Util from "../../util";

import { Images, Metrics, ApplicationStyles, Colors } from "../../theme";

class OrderItem extends Component {
  static propTypes = {
    onPress: PropTypes.func,
    orderItem: PropTypes.object
  };

  static defaultProps = {
    onPress: () => {},
    orderItem: {}
  };

  constructor(props) {
    super(props);
  }

  shouldComponentUpdate(nextProps) {
    return !_.isEqual(nextProps.orderItem, this.props.orderItem);
  }

  renderUserImage(image) {
    if (Util.isEmpty(image)) {
      return <CircleTextView title="H" />;
    }

    return (
      <ImageView
        isShowActivity={false}
        source={{ uri: image }}
        style={styles.circleImageStyle}
        customImagePlaceholderDefaultStyle={styles.circleImageStyle}
        borderRadius={Metrics.thumbImageRadius}
        placeholderSource={Images.imagePic1}
      />
    );
  }

  handleCheckboxPress = (index, sectionKey, checkBoxPress) => () => {
    checkBoxPress(index, sectionKey);
  };

  renderCheckBox(isDelivered, index, sectionKey, checkBoxPress) {
    return (
      <TouchableWithoutFeedback
        onPress={this.handleCheckboxPress(index, sectionKey, checkBoxPress)}
      >
        <View style={styles.checkImage}>
          <Image
            source={
              isDelivered
                ? Images.orderItemSelected
                : Images.orderItemUnSelected
            }
            style={styles.checkImage}
            resizeMode="contain"
          />
        </View>
      </TouchableWithoutFeedback>
    );
  }

  renderPaymentDetail(
    paymentDetail,
    index,
    sectionKey,
    checkBoxPress,
    isChecked
  ) {
    const { orderId, paid, paymentMethod, isDelivered } = paymentDetail;
    return (
      <View style={styles.paymentWrapper}>
        <Text
          style={[
            ApplicationStyles.sB16Black,
            { flex: 1, marginRight: Metrics.baseMargin }
          ]}
        >
          {`RH${orderId} | $${paid} | ${paymentMethod}`}
        </Text>
        {this.renderCheckBox(isChecked, index, sectionKey, checkBoxPress)}
      </View>
    );
  }

  renderOrderDetail(orderItem, index, sectionKey, checkBoxPress) {
    const { name, distance, image, placeName, paymentDetail } = orderItem;
    return (
      <View style={styles.orderDetailWrapper}>
        <View style={styles.personDetailWrapper}>
          <Text
            style={[
              ApplicationStyles.dBold16,
              { flex: 1, marginRight: Metrics.smallMargin }
            ]}
            numberOfLines={1}
          >
            {name}
          </Text>
          {distance && (
            <Text style={ApplicationStyles.re13Black}>{`${distance}mi`}</Text>
          )}
        </View>
        <Text
          style={[
            ApplicationStyles.re15Gray,
            { paddingBottom: Metrics.smallMargin, paddingTop: Metrics.ratio(2) }
          ]}
        >
          {placeName}
        </Text>

        {this.renderPaymentDetail(
          paymentDetail,
          index,
          sectionKey,
          checkBoxPress,
          orderItem.isChecked
        )}
      </View>
    );
  }

  render() {
    const {
      onPress,
      index,
      orderItem,
      sectionKey,
      checkBoxPress,
      ...rest
    } = this.props;
    return (
      <ButtonView
        onPress={() => onPress(index, sectionKey)}
        style={styles.containerStyle}
        {...rest}
      >
        {this.renderUserImage(orderItem.image)}
        {this.renderOrderDetail(orderItem, index, sectionKey, checkBoxPress)}
      </ButtonView>
    );
  }
}

const mapStateToProps = () => ({});
const actions = {};

export default connect(mapStateToProps, actions)(OrderItem);
