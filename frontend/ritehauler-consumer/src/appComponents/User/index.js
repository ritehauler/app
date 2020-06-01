// @flow
import React from "react";
import PropTypes from "prop-types";
import { View } from "react-native";

import { Text, ImageLoad } from "../../components";
import { Metrics, Images } from "../../theme";
import styles from "./styles";

export default class User extends React.PureComponent {
  static propTypes = {
    image: PropTypes.oneOfType([PropTypes.number, PropTypes.object]).isRequired,
    name: PropTypes.string.isRequired,
    onPress: PropTypes.func
  };

  static defaultProps = {
    onPress: () => {}
  };

  render() {
    const { image, name, onPress } = this.props;
    return (
      <View style={styles.container} onPress={onPress}>
        <ImageLoad
          isShowActivity={false}
          borderRadius={Metrics.baseMargin}
          style={styles.image}
          customImagePlaceholderDefaultStyle={
            styles.customImagePlaceholderDefaultStyle
          }
          placeholderSource={Images.userPlaceholder}
          source={image}
        />
        <Text type="bold">{name}</Text>
      </View>
    );
  }
}
