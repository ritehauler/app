// @flow
import React from "react";
import { View, Image } from "react-native";
import PropTypes from "prop-types";
import Swipeout from "react-native-swipeout";

import { Images, Strings } from "../../../theme";
import styles from "./styles";
import { Text, ButtonView, Separator } from "../../../components";
import Util from "../../../util";

export default class Item extends React.PureComponent {
  static propTypes = {
    data: PropTypes.object.isRequired,
    index: PropTypes.number.isRequired,
    onPress: PropTypes.func.isRequired,
    isSelected: PropTypes.bool.isRequired,
    onPressDelete: PropTypes.func.isRequired,
    isSelectable: PropTypes.bool.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    this.onPressItem = this.onPressItem.bind(this);
  }

  onPressItem() {
    const { data, onPress, index, isSelectable } = this.props;
    if (isSelectable) {
      onPress(data, index);
    }
  }

  _renderCardInfo() {
    const { brand } = this.props.data;
    return (
      <View style={styles.cardImageAndUserName}>
        <Image source={Util.getCardImage(brand)} />
        <Text style={styles.userName} type="light" size="xSmall">
          {brand}
        </Text>
      </View>
    );
  }

  _renderCardNumber() {
    const { data, isSelected } = this.props;
    const { last4 } = data;
    return (
      <View style={styles.cardNumberContainer}>
        <Text
          style={styles.cardNumber}
          type="light"
          size="xSmall"
          color="secondary"
        >
          {Strings.cardAsterisk} {last4}
        </Text>
        {isSelected && <Image source={Images.check} />}
      </View>
    );
  }

  _renderSeparator() {
    return <Separator />;
  }

  render() {
    const { index, onPressDelete, isSelectable } = this.props;
    const swipeoutBtns = [
      {
        text: Strings.deleteString,
        type: "delete",
        onPress: () => onPressDelete(index)
      }
    ];
    const buttonProps = isSelectable
      ? { enableClick: true }
      : { disableRipple: true };
    return (
      <Swipeout right={swipeoutBtns} autoClose>
        <ButtonView
          style={styles.container}
          onPress={this.onPressItem}
          {...buttonProps}
        >
          {this._renderCardInfo()}
          {this._renderSeparator()}
          {this._renderCardNumber()}
        </ButtonView>
      </Swipeout>
    );
  }
}
