// @flow
import React from "react";
import PropTypes from "prop-types";
import { View } from "react-native";

import styles from "./styles";
import { Text } from "../../components";

export default class Info extends React.PureComponent {
  static propTypes = {
    title: PropTypes.string.isRequired,
    value: PropTypes.any.isRequired,
    isOrder: PropTypes.bool
  };

  static defaultProps = {
    isOrder: true
  };

  render() {
    const { isOrder, title, value } = this.props;
    const titleType = isOrder ? "semiBold" : "base";
    const titleSize = isOrder ? "xSmall" : "xSmall";
    const valueType = isOrder ? "light" : "medium";
    const valueSize = isOrder ? "normal" : "normal";

    return (
      <View style={styles.container}>
        <Text type={titleType} size={titleSize}>
          {title}
        </Text>
        <Text type={valueType} size={valueSize} style={styles.value}>
          {value}
        </Text>
      </View>
    );
  }
}
