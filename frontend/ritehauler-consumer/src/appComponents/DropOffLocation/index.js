// @flow
import React from "react";
import PropTypes from "prop-types";
import { Image, View } from "react-native";
import Collapsible from "react-native-collapsible";

import { Images, Strings } from "../../theme";
import { Text, ButtonView } from "../../components";
import styles from "./styles";

export default class DropOffLocation extends React.PureComponent {
  static propTypes = {
    collapsed: PropTypes.bool,
    isCollapsible: PropTypes.bool,
    text: PropTypes.string,
    numberOfLines: PropTypes.number,
    title: PropTypes.string,
    emptyText: PropTypes.string,
    onPress: PropTypes.func,
    disableRipple: PropTypes.bool
  };

  static defaultProps = {
    collapsed: false,
    isCollapsible: false,
    text: "",
    numberOfLines: 3,
    title: Strings.dropoffLocation,
    emptyText: Strings.dropoffEmpty,
    onPress: () => {},
    disableRipple: false
  };

  _renderImageView() {
    return (
      <View style={styles.imageContainer}>
        <View style={styles.line} />
        <Image source={Images.dropOff} style={styles.image} />
      </View>
    );
  }

  _renderContentView() {
    const { text, title, emptyText, numberOfLines } = this.props;

    if (text === "") {
      return (
        <Text color="secondary" size="xSmall" style={styles.where}>
          {emptyText}
        </Text>
      );
    }

    return (
      <View style={styles.contentContainer}>
        <Text size="small">{title}</Text>
        <Text
          color="secondary"
          size="xxSmall"
          numberOfLines={numberOfLines}
          style={styles.textDropOff}
        >
          {text}
        </Text>
      </View>
    );
  }

  _renderContainer() {
    const { onPress, disableRipple } = this.props;
    return (
      <ButtonView
        onPress={onPress}
        style={styles.container}
        disableRipple={disableRipple}
      >
        {this._renderImageView()}
        {this._renderContentView()}
      </ButtonView>
    );
  }

  render() {
    const { isCollapsible, collapsed } = this.props;

    if (isCollapsible) {
      return (
        <Collapsible collapsed={collapsed}>
          {this._renderContainer()}
        </Collapsible>
      );
    }

    return this._renderContainer();
  }
}
