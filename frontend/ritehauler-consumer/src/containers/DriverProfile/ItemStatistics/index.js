// @flow
import React from "react";
import { View } from "react-native";
import PropTypes from "prop-types";

import { Text, ImageLoad } from "../../../components";
import { Metrics } from "../../../theme";
import Util from "../../../util";
import styles from "./styles";

export default class ItemStatistics extends React.PureComponent {
  static propTypes = {
    data: PropTypes.object.isRequired
  };

  static defaultProps = {};

  _renderImage() {
    const { file } = this.props.data;
    return (
      <ImageLoad
        isShowActivity={false}
        borderRadius={Metrics.statisticsImage / 2}
        style={styles.image}
        customImagePlaceholderDefaultStyle={
          styles.customImagePlaceholderDefaultStyle
        }
        source={{ uri: Util.getImageFromGalleryObject(file, 1) }}
      />
    );
  }

  _renderText() {
    const { option } = this.props.data;
    return (
      <Text textAlign="center" size="xxSmall" type="light" style={styles.text}>
        {option || ""}
      </Text>
    );
  }

  _renderCount() {
    const { count } = this.props.data;
    return (
      <View style={styles.badge}>
        <Text color="tertiary" size="xxxSmall" type="light">
          {count || 0}
        </Text>
      </View>
    );
  }

  render() {
    return (
      <View style={styles.container}>
        {this._renderImage()}
        {this._renderText()}
        {this._renderCount()}
      </View>
    );
  }
}
