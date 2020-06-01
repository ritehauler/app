// @flow
import _ from "lodash";
import React, { Component } from "react";
import { View, Image } from "react-native";
import PropTypes from "prop-types";

import { Images } from "../../../theme";
import styles from "./styles";
import { Text, ButtonView, Separator } from "../../../components";

export default class Item extends Component {
  static propTypes = {
    data: PropTypes.object.isRequired,
    onPress: PropTypes.func.isRequired,
    isSelected: PropTypes.bool.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    this.onPressItem = this.onPressItem.bind(this);
  }

  shouldComponentUpdate(nextProps) {
    return !_.isEqual(nextProps, this.props);
  }

  onPressItem() {
    const { onPress, data } = this.props;
    onPress(data);
  }

  _renderItem() {
    const { data, isSelected } = this.props;
    return (
      <View style={styles.itemContainer}>
        <Text style={styles.title} size="small">
          {data.title}
        </Text>
        {isSelected && <Image source={Images.check} />}
      </View>
    );
  }

  _renderSeparator() {
    return <Separator style={styles.separator} />;
  }

  render() {
    return (
      <ButtonView
        style={styles.container}
        onPress={this.onPressItem}
        enableClick
      >
        {this._renderItem()}
        {this._renderSeparator()}
      </ButtonView>
    );
  }
}
