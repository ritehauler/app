// @flow
import React from "react";
import PropTypes from "prop-types";
import { Image, View } from "react-native";

import { Strings } from "../../theme";
import { ButtonView, Text } from "../../components";
import styles from "./styles";

export default class RightViewNavigation extends React.PureComponent {
  static propTypes = {
    action: PropTypes.func,
    text: PropTypes.string,
    image: PropTypes.number,
    badgeCount: PropTypes.number
  };

  static defaultProps = {
    action: () => {},
    text: Strings.save,
    image: 0,
    badgeCount: 0
  };

  state = {
    badgeCount: this.props.badgeCount
  };

  setBadgeCount = badgeCount => {
    this.setState({ badgeCount });
  };

  _renderText() {
    const { text, image } = this.props;
    if (image === 0) {
      return (
        <Text type="base2" color="accent" size="small">
          {text}
        </Text>
      );
    }
    return null;
  }

  _renderImage() {
    const { image } = this.props;
    if (image !== 0) {
      return <Image source={image} />;
    }
    return null;
  }

  _renderBadgeCount(badgeCount) {
    return (
      <View style={styles.badgeContainer}>
        <Text size="small" color="tertiary" type="bold">
          {badgeCount}
        </Text>
      </View>
    );
  }

  render() {
    const { action } = this.props;
    const { badgeCount } = this.state;
    return (
      <ButtonView
        onPress={action}
        style={styles.container}
        isBackgroundBorderLess
      >
        {this._renderText()}
        {this._renderImage()}
        {badgeCount !== 0 && this._renderBadgeCount(badgeCount)}
      </ButtonView>
    );
  }
}
