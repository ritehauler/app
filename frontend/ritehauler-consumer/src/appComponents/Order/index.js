// @flow
import React from "react";
import { View, Image } from "react-native";
import PropTypes from "prop-types";

import { Images, Strings } from "../../theme";
import styles from "./styles";
import { Text, ButtonView } from "../../components";

export default class Facebook extends React.PureComponent {
  static propTypes = {
    onPress: PropTypes.func
  };

  static defaultProps = {
    onPress: () => {}
  };

  render() {
    const { onPress } = this.props;
    return (
      <ButtonView style={styles.container} onPress={onPress}>
        <Image source={Images.fb} />
        <View style={styles.midLine} />
        <Text size="large" type="bold2" color="tertiary">
          {Strings.signInWithFacebook}
        </Text>
      </ButtonView>
    );
  }
}
