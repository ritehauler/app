// @flow
import React from "react";
import { View, Image } from "react-native";
import PropTypes from "prop-types";

import { Images } from "../../../theme";
import styles from "./styles";
import { Text } from "../../../components";

export default class ItemReview extends React.PureComponent {
  static propTypes = {
    data: PropTypes.object.isRequired
  };

  static defaultProps = {};

  _renderImage() {
    return <Image source={Images.quote} style={styles.image} />;
  }

  _renderText() {
    const { review } = this.props.data;
    return (
      <Text style={styles.text} type="light" size="xSmall">
        {review || ""}
      </Text>
    );
  }

  render() {
    return (
      <View style={styles.container}>
        {this._renderImage()}
        {this._renderText()}
      </View>
    );
  }
}
