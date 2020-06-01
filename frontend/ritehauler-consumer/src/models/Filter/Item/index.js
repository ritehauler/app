// @flow
import React from "react";
import { View, Image } from "react-native";
import PropTypes from "prop-types";

import { Images } from "../../../theme";
import styles from "./styles";
import { Text, ButtonView, Separator } from "../../../components";

export default class Item extends React.PureComponent {
  static propTypes = {
    data: PropTypes.object.isRequired,
    onPress: PropTypes.func.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    this.onPressItem = this.onPressItem.bind(this);
  }

  onPressItem() {
    this.props.onPress(this.props.data);
  }

  _renderItem() {
    const { title } = this.props.data;
    return (
      <Text style={styles.title} size="small">
        {title}
      </Text>
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
