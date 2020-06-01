// @flow
import _ from "lodash";
import React, { Component } from "react";
import { View, Image } from "react-native";
import PropTypes from "prop-types";
import Swipeout from "react-native-swipeout";

import { Images, Strings } from "../../theme";
import styles from "./styles";
import { Text, ButtonView, Separator } from "../../components";
import Utils from "../../util";

export default class OrderItem extends Component {
  static propTypes = {
    data: PropTypes.object.isRequired,
    index: PropTypes.number.isRequired,
    onPressItem: PropTypes.func,
    disableRipple: PropTypes.bool,
    onPressDelete: PropTypes.func,
    showSwipeOut: PropTypes.bool
  };

  static defaultProps = {
    onPressItem: undefined,
    disableRipple: false,
    onPressDelete: undefined,
    showSwipeOut: false
  };

  constructor(props) {
    super(props);
    this.onPressItem = this.onPressItem.bind(this);
  }

  shouldComponentUpdate(nextProps: Object) {
    return !_.isEqual(nextProps, this.props);
  }

  onPressItem() {
    if (this.props.onPressItem) {
      this.props.onPressItem(this.props.data, this.props.index);
    }
  }

  _renderSeparator() {
    return <Separator style={styles.separator} />;
  }

  _renderName() {
    const { itemBox, itemName, otherItemText, showReceipt } = this.props.data;
    const actualItemName =
      otherItemText && otherItemText !== ""
        ? otherItemText
        : itemName.title || "";

    const boxName = itemBox.title || itemBox.value || "";
    return (
      <View style={styles.nameContainer}>
        <Text type="semiBold" numberOfLines={1} size="small">
          {actualItemName} ({boxName})
        </Text>
        {showReceipt && (
          <Image source={Images.expensive} style={styles.expensive} />
        )}
      </View>
    );
  }

  _renderDetail() {
    const { width, height, length, weight, volume } = this.props.data;
    return (
      <Text size="xSmall" numberOfLines={1} type="light">
        {width}x{height}x{length}, {weight}{" "}
        {Utils.makeStringSingular(Strings.unitNameWeight, weight, 1)}, {volume}{" "}
        {Utils.makeStringSingular(Strings.volumeUnit, volume, 2)}
      </Text>
    );
  }

  _renderInfo() {
    return (
      <View style={styles.info}>
        {this._renderName()}
        {this._renderDetail()}
      </View>
    );
  }

  _renderCount() {
    const { quantity } = this.props.data;
    return (
      <Text size="small" color="secondary">
        x{quantity}
      </Text>
    );
  }

  _renderItem() {
    return (
      <View style={styles.content}>
        {this._renderInfo()}
        {this._renderCount()}
      </View>
    );
  }

  _renderExtraItemInfo() {
    const { per_extra_item_charge } = this.props.data;
    if (
      per_extra_item_charge &&
      per_extra_item_charge !== "" &&
      per_extra_item_charge !== "0"
    ) {
      return (
        <Text style={styles.extraItem} size="xSmall" color="accent">
          {Utils.getFormattedPrice(per_extra_item_charge)}{" "}
          {Strings.chargeEveryExtraItem}
        </Text>
      );
    }
    return null;
  }

  _renderContent() {
    const { disableRipple } = this.props;
    return (
      <ButtonView
        style={styles.container}
        enableClick
        onPress={this.onPressItem}
        disableRipple={disableRipple}
      >
        {this._renderItem()}
        {this._renderExtraItemInfo()}
        {this._renderSeparator()}
      </ButtonView>
    );
  }

  render() {
    const { showSwipeOut, onPressDelete, index } = this.props;

    if (!showSwipeOut) {
      return this._renderContent();
    }

    const swipeoutBtns = [
      {
        text: Strings.deleteString,
        type: "delete",
        onPress: () => onPressDelete(index)
      }
    ];
    return (
      <Swipeout right={swipeoutBtns} autoClose>
        {this._renderContent()}
      </Swipeout>
    );
  }
}
