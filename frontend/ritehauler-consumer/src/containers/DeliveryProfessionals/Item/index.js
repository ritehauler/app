// @flow
import React, { Component } from "react";
import { View, Image } from "react-native";
import PropTypes from "prop-types";

import { Text, ButtonView, Separator, ImageServer } from "../../../components";
import { DeliveryProfessionalInfo } from "../../../appComponents";
import { Images } from "../../../theme";
import styles from "./styles";
import Utils from "../../../util";

export default class Item extends Component {
  static propTypes = {
    data: PropTypes.array.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    this.updateDeliveryProfessionalCount = this.updateDeliveryProfessionalCount.bind(
      this
    );
  }

  state = {
    data: this.props.data[0],
    isPlusDisable: false,
    isMinusDisable: true,
    currentIndex: 0
  };

  getSelectedDeliveryProfessional = () => this.state.data;

  updateDeliveryProfessionalCount(number) {
    const currentIndex = this.state.currentIndex + number;
    const isMinusDisable = currentIndex === 0;
    const isPlusDisable = currentIndex === this.props.data.length - 1;
    this.setState({
      currentIndex,
      data: this.props.data[currentIndex],
      isMinusDisable,
      isPlusDisable
    });
  }

  _renderButton(isDisable, image, count) {
    const { data } = this.props;
    if (data.length === 0) {
      return null;
    }
    if (isDisable) {
      return <Image source={image} style={styles.disable} />;
    }
    return (
      <ButtonView
        enableClick
        onPress={() => this.updateDeliveryProfessionalCount(count)}
      >
        <Image source={image} />
      </ButtonView>
    );
  }

  _renderImage() {
    const { gallery } = this.state.data;
    return (
      <View style={styles.imageBox}>
        <ImageServer
          style={styles.image}
          source={{ uri: Utils.getImageFromGallery(gallery, 1) }}
          resizeMode="contain"
        />
      </View>
    );
  }

  _renderImageContainer() {
    const { isPlusDisable, isMinusDisable } = this.state;
    return (
      <View style={styles.imageContainer}>
        {this._renderButton(isMinusDisable, Images.minus, -1)}
        {this._renderImage()}
        {this._renderButton(isPlusDisable, Images.plus, 1)}
      </View>
    );
  }

  _renderSeparator() {
    return <Separator />;
  }

  _renderPriceAndCount() {
    const { price, number_of_labour } = this.state.data;
    return (
      <DeliveryProfessionalInfo
        deliveryProfessionals={number_of_labour}
        loadingPrice={Utils.getFormattedPrice(price)}
        isOrder={false}
      />
    );
  }

  _renderInfo() {
    const { description } = this.state.data;
    return (
      <Text style={styles.info} type="light" size="xSmall">
        {description || ""}
      </Text>
    );
  }

  render() {
    return (
      <View>
        {this._renderImageContainer()}
        {this._renderSeparator()}
        {this._renderPriceAndCount()}
        {this._renderSeparator()}
        {this._renderInfo()}
      </View>
    );
  }
}
